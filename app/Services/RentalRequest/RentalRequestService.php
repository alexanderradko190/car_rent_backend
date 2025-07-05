<?php

namespace App\Services\RentalRequest;

use App\DTO\RentalRequest\CreateRentalRequestDTO;
use App\Enums\Car\CarStatus;
use App\Enums\Car\RentalStatus;
use App\Models\RentalRequest\RentalRequest;
use App\Repositories\Car\CarRepository;
use App\Repositories\Client\ClientRepository;
use App\Repositories\RentalRequest\RentalRequestRepository;
use App\Services\RentalCostCalculator;
use App\Services\RentHistory\RentHistoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RentalApprovedNotification;
use Illuminate\Support\Facades\Storage;

class RentalRequestService
{
    public function __construct(
        private RentalRequestRepository $repository,
        private CarRepository           $carRepo,
        private ClientRepository        $clientRepo,
        private RentalCostCalculator    $costCalculator
    )
    {
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
            return ['error' => 'Автомобиль уже забронирован на этот период'];
        }

        $car = $this->carRepo->find($dto->car_id);

        if (!$car) {
            return ['error' => 'Автомобиль не найден'];
        }

        $total = $this->costCalculator->calculate($car, $dto->start_time, $dto->end_time, $dto->insurance_option);

        $data = [
            'client_id' => $dto->client_id,
            'car_id' => $dto->car_id,
            'start_time' => $dto->start_time,
            'end_time' => $dto->end_time,
            'total_cost' => $total,
            'insurance_option' => $dto->insurance_option,
            'status' => RentalStatus::PENDING->value,
        ];

        $request = $this->repository->create($data);
        $request = $this->repository->withClient($request->id);
        $request = $this->repository->withCar($request->id);

        $agreementPath = $this->generateAgreement($request);
        $request->agreement_path = $agreementPath;
        $request->save();

        return ['data' => $request];
    }

    public function approve($id): array
    {
        $request = $this->repository->find($id);

        if (!$request) {
            return ['error' => 'Заявка не найдена'];
        }

        if ($request->status !== RentalStatus::PENDING) {
            return ['error' => 'Заявка уже обработана'];
        }

        $request->status = RentalStatus::APPROVED->value;
        $request->save();

        $car = $this->carRepo->find($request->car_id);
        $this->carRepo->update($car, ['status' => CarStatus::RENTED->value, 'client_id' => $request->client_id]);

        $client = $this->clientRepo->find($request->client_id);
        if ($client && $client->email) {
            Notification::route('mail', $client->email)->notify(new RentalApprovedNotification($request));
        }

        return ['message' => 'Заявка одобрена', 'data' => $request];
    }

    public function reject($id): array
    {
        $request = $this->repository->find($id);

        if (!$request) {
            return ['error' => 'Заявка не найдена'];
        }

        if ($request->status !== RentalStatus::PENDING) {
            return ['error' => 'Заявка уже обработана'];
        }

        $request->status = RentalStatus::REJECTED->value;
        $request->save();

        return ['message' => 'Заявка отклонена'];
    }

    public function complete($id): array
    {
        $request = $this->repository->withClient($id);

        if (!$request) {
            return ['error' => 'Заявка не найдена'];
        }

        if ($request->status !== RentalStatus::APPROVED) {
            return ['error' => 'Аренда должна быть одобрена для завершения'];
        }

        $request->status = RentalStatus::COMPLETED->value;
        $request->save();

        $historyData = [
            'car_id' => $request->car_id,
            'client_id' => $request->client_id,
            'start_time' => $request->start_time,
            'end_time' => now(),
            'total_cost' => $request->total_cost,
        ];

        app(RentHistoryService::class)->create($historyData);

        return ['message' => 'Аренда завершена и добавлена в историю', 'data' => $request];
    }

    public function delete($id): array
    {
        $request = $this->repository->find($id);

        if (!$request) {
            return ['error' => 'Заявка не найдена'];
        }
        $this->repository->delete($request);

        return ['message' => 'Заявка удалена'];
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
