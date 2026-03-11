<?php

namespace App\Listeners\RentalRequest;

use App\Events\RentalRequest\RentalRequestIsCompleted;
use App\Mail\RentalCompletedMail;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendRentalCompletedEmail implements ShouldQueue
{
    public string $queue = 'send-mail';

    public function handle(RentalRequestIsCompleted $event): void
    {
        $email = $event->client->email;

        if (!$email) {
            throw new Exception('У пользователя не указан email');
        }

        try {
            Mail::to($email)->send(new RentalCompletedMail(
                rentHistory: $event->rentHistory,
                car: $event->car,
                client: $event->client,
                agreementPath: $event->rentalRequest->agreement_path ?? null,
            ));
        } catch (Throwable $e) {
            throw new Exception('Не удалось отправить письмо о завершении аренды', 0, $e);
        }
    }
}
