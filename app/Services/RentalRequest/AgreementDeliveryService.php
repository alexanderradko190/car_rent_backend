<?php

namespace App\Services\RentalRequest;

use App\Enums\Report\ReportStatus;
use App\Mail\RentalCompletedMail;
use App\Models\Car\Car;
use App\Models\Client\Client;
use App\Models\RentHistory\RentHistory;
use App\Services\RentHistory\RentHistoryService;
use App\Models\RentalRequest\AgreementDelivery;
use App\Models\RentalRequest\RentalRequest;
use App\Helpers\TransactionHelper;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class AgreementDeliveryService
{
    public function __construct(
        private readonly AgreementService $agreementService,
        private readonly RentHistoryService $rentHistoryService,
        private readonly TransactionHelper $transaction
    ) {
        //
    }
    public function sendForCompletion(
        RentalRequest $rentalRequest,
        RentHistory $rentHistory,
        Car $car,
        Client $client,
    ): AgreementDelivery {
        return $this->send($rentalRequest, $rentHistory, $car, $client, false);
    }

    public function sendForRentalRequestId(
        int $rentalRequestId,
        ?int $rentHistoryId = null
    ): AgreementDelivery
    {
        $rentalRequest = RentalRequest::query()
            ->with(['car', 'client'])
            ->find($rentalRequestId);

        if (!$rentalRequest) {
            throw new RuntimeException('Заявка на аренду не найдена', 404);
        }

        $rentHistory = $rentHistoryId
            ? $this->rentHistoryService->findWithCarAndClient($rentHistoryId)
            : null;
        $rentHistory ??= $this->findOrCreateRentHistoryForRequest($rentalRequest);

        $car = $rentalRequest->car ?? $rentHistory->car;
        $client = $rentalRequest->client ?? $rentHistory->client;

        if (!$client) {
            throw new RuntimeException('Клиент не найден', 404);
        }

        return $this->send($rentalRequest, $rentHistory, $car, $client);
    }

    private function send(
        RentalRequest $rentalRequest,
        RentHistory $rentHistory,
        Car $car,
        Client $client
    ): AgreementDelivery {
        $attempt = $this->createAttempt($rentalRequest, $rentHistory, $car, $client);

        if (!$attempt->wasRecentlyCreated) {
            return $attempt;
        }

        try {
            if (!$client->email) {
                throw new RuntimeException('У пользователя не указан email', 422);
            }

            $agreementPath = $this->agreementService->ensureAgreementExists($rentalRequest);

            $attempt->update([
                'agreement_path' => $agreementPath,
                'generated_at' => now(),
            ]);

            Mail::to($client->email)->send(new RentalCompletedMail(
                rentHistory: $rentHistory,
                car: $car,
                client: $client,
                agreementPath: $agreementPath,
            ));

            $attempt->update([
                'status' => ReportStatus::FINISHED->value,
                'sent_at' => now(),
            ]);
        } catch (Throwable $e) {
            $attempt->update([
                'status' => ReportStatus::FAILED->value,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Не удалось отправить договор аренды', 500, $e);
        }

        return $attempt;
    }

    private function createAttempt(
        RentalRequest $rentalRequest,
        RentHistory $rentHistory,
        Car $car,
        Client $client,
    ): AgreementDelivery {
        return $this->transaction->run(function () use ($rentalRequest, $rentHistory, $car, $client) {
            RentalRequest::query()
                ->whereKey($rentalRequest->id)
                ->lockForUpdate()
                ->first();

            $existing = AgreementDelivery::query()
                ->where('rental_request_id', $rentalRequest->id)
                ->where('rent_history_id', $rentHistory->id)
                ->where('status', ReportStatus::PROCESSING->value)
                ->orderByDesc('id')
                ->first();

            if ($existing) {
                return $existing;
            }

            return AgreementDelivery::create([
                'rental_request_id' => $rentalRequest->id,
                'rent_history_id' => $rentHistory->id ?? null,
                'car_id' => $car->id ?? null,
                'client_id' => $client->id ?? null,
                'status' => ReportStatus::PROCESSING->value,
                'agreement_path' => $rentalRequest->agreement_path,
            ]);
        });
    }

    private function findOrCreateRentHistoryForRequest(RentalRequest $rentalRequest): RentHistory
    {
        return $this->rentHistoryService->create([
            'rental_request_id' => $rentalRequest->id,
            'car_id' => $rentalRequest->car_id,
            'client_id' => $rentalRequest->client_id,
            'start_time' => $rentalRequest->start_time,
            'end_time' => $rentalRequest->end_time,
            'total_cost' => $rentalRequest->total_cost ?? 0,
        ]);
    }
}
