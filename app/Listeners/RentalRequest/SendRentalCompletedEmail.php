<?php

namespace App\Listeners\RentalRequest;

use App\Events\RentalRequest\RentalRequestIsCompleted;
use App\Services\RentalRequest\AgreementDeliveryService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRentalCompletedEmail implements ShouldQueue
{
    public string $queue = 'send-mail';

    public function __construct(
        private AgreementDeliveryService $agreementDeliveryService
    ) {
        //
    }

    public function handle(RentalRequestIsCompleted $event): void
    {
        $this->agreementDeliveryService->sendForCompletion(
            $event->rentalRequest,
            $event->rentHistory,
            $event->car,
            $event->client
        );
    }
}
