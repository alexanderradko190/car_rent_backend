<?php

namespace App\Console\Commands;

use App\Services\RentalRequest\AgreementDeliveryService;
use Illuminate\Console\Command;
use RuntimeException;
use Throwable;

class SendAgreement extends Command
{
    protected $signature = 'rental:send-agreement {rental_request_id} {--rent-history-id=}';

    protected $description = 'Принудительная отправка договора аренды по заявке';

    public function handle(AgreementDeliveryService $service): int
    {
        $rentalRequestId = $this->argument('rental_request_id');
        $rentHistoryId = $this->option('rent-history-id');

        try {
            $attempt = $service->sendForRentalRequestId(
                $rentalRequestId,
                $rentHistoryId ?? null
            );

            $this->info('Договор отправлен. Delivery ID: ' . $attempt->id);

            return self::SUCCESS;
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error('Неожиданная ошибка: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
