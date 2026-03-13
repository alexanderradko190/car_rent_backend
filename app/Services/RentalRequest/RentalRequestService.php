<?php

namespace App\Services\RentalRequest;

use App\DTO\RentalRequest\CreateRentalRequestDTO;
use App\Enums\Car\CarStatus;
use App\Enums\Car\RentalStatus;
use App\Events\RentalRequest\RentalRequestIsCompleted;
use App\Exceptions\ServiceException;
use App\Models\RentalRequest\RentalRequest;
use App\Repositories\Car\CarRepositoryInterface;
use App\Repositories\Client\ClientRepository;
use App\Repositories\RentalRequest\RentalRequestRepository;
use App\Helpers\TransactionHelper;
use App\Services\RentalRequest\AgreementService;
use App\Services\RentalCostCalculator;
use App\Services\RentHistory\RentHistoryService;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;
use Throwable;

class RentalRequestService
{
    public function __construct(
        private RentalRequestRepository $repository,
        private CarRepositoryInterface   $carRepository,
        private ClientRepository        $clientRepository,
        private AgreementService        $agreementService,
        private RentalCostCalculator    $costCalculator,
        private RentHistoryService    $rentHistoryService,
        private TransactionHelper    $transaction
    ) {
        //
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find($id): ?RentalRequest
    {
        return $this->repository->find($id);
    }

    public function create(CreateRentalRequestDTO $dto): RentalRequest
    {
        if ($this->repository->intersecting($dto->car_id, $dto->start_time, $dto->end_time)) {
            throw new ServiceException('Автомобиль уже забронирован на этот период', 400);
        }

        $car = $this->carRepository->find($dto->car_id);

        if (!$car) {
            throw new ServiceException('Автомобиль не найден', 404);
        }

        $total = $this->costCalculator->calculate($car, $dto->start_time, $dto->end_time, $dto->insurance_option);

        $data = [
            'client_id' => $dto->client_id,
            'car_id' => $dto->car_id,
            'start_time' => $dto->start_time,
            'end_time' => $dto->end_time,
            'total_cost' => $total,
            'insurance_option' => $dto->insurance_option,
            'status' => RentalStatus::PENDING->value
        ];

        $request = $this->transaction->run(function () use ($data, $car, $dto) {
            $request = $this->repository->create($data);

            $this->carRepository->update($car, [
                'status' => CarStatus::RENTED->value,
                'current_renter_id' => $dto->client_id,
            ]);

            $requestWithRelations = $this->repository->findWithClientAndCar($request->id);
            $this->agreementService->ensureAgreementExists($requestWithRelations);

            return $requestWithRelations;
        });

        return $request;
    }

    public function approve($id): RentalRequest
    {
        $request = $this->repository->find($id);

        if (!$request) {
            throw new ServiceException('Заявка не найдена', 404);
        }

        if ($request->status->value !== RentalStatus::PENDING->value) {
            throw new ServiceException('Заявка уже обработана', 400);
        }

        $this->transaction->run(function () use ($request) {
            $request->status = RentalStatus::APPROVED->value;
            $request->save();

            $car = $this->carRepository->find($request->car_id);

            if ($car) {
                $this->carRepository->update($car, [
                    'status' => CarStatus::RENTED->value,
                    'current_renter_id' => $request->client_id,
                ]);
            }
        });

        return $request->fresh(['car', 'client']) ?? $request;
    }

    public function reject($id): RentalRequest
    {
        $request = $this->repository->find($id);

        if (!$request) {
            throw new ServiceException('Заявка не найдена', 404);
        }

        if ($request->status->value !== RentalStatus::PENDING->value) {
            throw new ServiceException('Заявка уже обработана', 400);
        }

        $this->transaction->run(function () use ($request) {
            $request->status = RentalStatus::REJECTED->value;
            $request->save();

            $car = $this->carRepository->find($request->car_id);

            if ($car && $car->current_renter_id === $request->client_id) {
                $this->carRepository->update($car, [
                    'status' => CarStatus::AVAILABLE->value,
                    'current_renter_id' => null,
                ]);
            }
        });

        return $request->fresh(['car', 'client']) ?? $request;
    }

    public function complete($id): RentalRequest
    {
        $rentalRequest = $this->repository->findWithClientAndCar($id);

        if (!$rentalRequest) {
            throw new ServiceException('Заявка не найдена', 404);
        }

        if ($rentalRequest->status->value !== RentalStatus::APPROVED->value) {
            throw new ServiceException('Аренда должна быть одобрена для завершения', 400);
        }

        try {
            $rentHistory = null;

            $this->transaction->run(function () use (&$rentHistory, $rentalRequest) {
                $rentalRequest->status = RentalStatus::COMPLETED->value;
                $rentalRequest->end_time = now();
                $rentalRequest->save();

                $rentHistory = $this->rentHistoryService->create([
                    'rental_request_id' => $rentalRequest->id,
                    'car_id'     => $rentalRequest->car_id,
                    'client_id'  => $rentalRequest->client_id,
                    'start_time' => $rentalRequest->start_time,
                    'end_time'   => $rentalRequest->end_time,
                    'total_cost' => $rentalRequest->total_cost,
                ]);

                if (!$rentHistory || !$rentHistory->id) {
                    throw new RuntimeException('Не удалось сохранить историю аренды');
                }

                $this->carRepository->update($rentalRequest->car, [
                    'status' => CarStatus::AVAILABLE->value,
                    'current_renter_id' => null,
                ]);
            });

            $this->agreementService->ensureAgreementExists($rentalRequest);

            event(new RentalRequestIsCompleted(
                rentalRequest: $rentalRequest,
                rentHistory: $rentHistory,
                car: $rentalRequest->car->fresh(),
                client: $rentalRequest->client
            ));

            return $rentalRequest->fresh(['car', 'client']) ?? $rentalRequest;
        } catch (Throwable $e) {
            throw new ServiceException('Не удалось завершить аренду', 500, $e);
        }
    }

    public function delete($id): void
    {
        $request = $this->repository->find($id);

        if (!$request) {
            throw new ServiceException('Заявка не найдена', 404);
        }

        if (in_array($request->status->value, [
            RentalStatus::APPROVED->value,
            RentalStatus::COMPLETED->value,
        ], true)) {
            throw new ServiceException(
                'Можно удалить заявку только в статусах "На рассмотрении" или "Отклонена"',
                400
            );
        }

        $this->repository->delete($request);
    }
}
