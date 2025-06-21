<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RentalRequest;
use Illuminate\Notifications\Messages\MailMessage;

class RentalApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(private RentalRequest $request)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ваша заявка на аренду одобрена')
            ->line('Ваша заявка на аренду автомобиля ' . $this->request->car->make . ' ' . $this->request->car->model . ' одобрена.')
            ->action('Скачать договор', url('/storage/' . $this->request->agreement_path));
    }
}
