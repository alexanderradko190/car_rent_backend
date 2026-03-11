<?php

namespace App\Services\RentalRequest;

use App\DTO\RentalRequest\CreateRentalRequestDTO;
use App\Enums\Car\CarStatus;
use App\Enums\Car\RentalStatus;
use App\Events\RentalRequest\RentalRequestIsCompleted;
use App\Models\RentalRequest\RentalRequest;
use App\Repositories\Car\CarRepositoryInterface;
use App\Repositories\Client\ClientRepository;
use App\Repositories\RentalRequest\RentalRequestRepository;
use App\Services\RentalCostCalculator;
use App\Services\RentHistory\RentHistoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class RentalRequestService
{
    public function __construct(
        private RentalRequestRepository $repository,
        private CarRepositoryInterface   $carRepo,
        private ClientRepository        $clientRepo,
        private RentalCostCalculator    $costCalculator,
        private RentHistoryService    $rentHistoryService
    ) {
        //
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateRentalRequestDTO $dto): array
    {
        if ($this->repository->intersecting($dto->car_id, $dto->start_time, $dto->end_time)) {

            return [
                'error' => 'Автомобиль уже забронирован на этот период'
            ];
        }

        $car = $this->carRepo->find($dto->car_id);

        if (!$car) {
            return [
                'error' => 'Автомобиль не найден'
            ];
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

        $request = $this->repository->create($data);
        $request = $this->repository->withClient($request->id);
        $request = $this->repository->withCar($request->id);

        $agreementPath = $this->generateAgreement($request);
        $request->agreement_path = $agreementPath;

        $request->save();

        return [
            'data' => $request
        ];
    }

    public function approve($id): array
    {
        $request = $this->repository->find($id);

        if (!$request) {
            return [
                'error' => 'Заявка не найдена'
            ];
        }

        if ($request->status->value !== RentalStatus::PENDING->value) {
            return [
                'error' => 'Заявка уже обработана'
            ];
        }

        $request->status = RentalStatus::APPROVED->value;
        $request->save();

        $car = $this->carRepo->find($request->car_id);

        if ($car) {
            $this->carRepo->update($car, [
                'status' => CarStatus::RENTED->value,
                'current_renter_id' => $request->client_id,
            ]);
        }

        return [
            'data' => $request
        ];
    }

    public function reject($id): array
    {
        $request = $this->repository->find($id);

        if (!$request) {
            return [
                'error' => 'Заявка не найдена'
            ];
        }

        if ($request->status->value !== RentalStatus::PENDING->value) {
            return [
                'error' => 'Заявка уже обработана'
            ];
        }

        $request->status = RentalStatus::REJECTED->value;

        $request->save();

        return [
            'message' => 'Заявка отклонена'
        ];
    }

    public function complete($id): array
    {
        $rentalRequest = $this->repository->findWithClientAndCar($id);

        if (!$rentalRequest) {
            return [
                'error' => 'Заявка не найдена'
            ];
        }

        if ($rentalRequest->status->value !== RentalStatus::APPROVED->value) {
            return [
                'error' => 'Аренда должна быть одобрена для завершения'
            ];
        }

        try {
            $rentHistory = null;

            DB::transaction(function () use (&$rentHistory, $rentalRequest) {
                $rentalRequest->status = RentalStatus::COMPLETED->value;
                $rentalRequest->end_time = now();
                $rentalRequest->save();

                $rentHistory = $this->rentHistoryService->create([
                    'car_id'     => $rentalRequest->car_id,
                    'client_id'  => $rentalRequest->client_id,
                    'start_time' => $rentalRequest->start_time,
                    'end_time'   => $rentalRequest->end_time,
                    'total_cost' => $rentalRequest->total_cost,
                ]);

                if (!$rentHistory || !$rentHistory->id) {
                    throw new RuntimeException('Не удалось сохранить историю аренды');
                }

                $this->carRepo->update($rentalRequest->car, [
                    'status' => CarStatus::AVAILABLE->value,
                    'current_renter_id' => null,
                ]);
            });

            event(new RentalRequestIsCompleted(
                rentalRequest: $rentalRequest,
                rentHistory: $rentHistory,
                car: $rentalRequest->car->fresh(),
                client: $rentalRequest->client
            ));

            return [
                'message' => 'Аренда завершена'
            ];
        } catch (Throwable $e) {
            return [
                'error' => 'Не удалось завершить аренду'
            ];
        }
    }

    public function delete($id): array
    {
        $request = $this->repository->find($id);

        if (!$request) {
            return [
                'error' => 'Заявка не найдена'
            ];
        }

        if ($request->status->value === (RentalStatus::APPROVED->value || RentalStatus::COMPLETED->value)) {
            return [
                'error' => 'Можно удалить заявку только в статусах "На рассмотрении" или "Отклонена"'
            ];
        }

        $this->repository->delete($request);

        return [
            'message' => 'Заявка удалена'
        ];
    }

    private function generateAgreement(RentalRequest $request): string
    {
        $pdf = Pdf::loadView('pdf.agreement', [
            'request' => $request,
            'car' => $request->car,
            'client' => $request->client
        ]);

        $fileName = 'agreements/agreement_' . $request->id . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        return $fileName;
    }
}
