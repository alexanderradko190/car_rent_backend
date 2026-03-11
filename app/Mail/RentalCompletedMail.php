<?php

namespace App\Mail;

use App\Models\Car\Car;
use App\Models\Client\Client;
use App\Models\RentHistory\RentHistory;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class RentalCompletedMail extends Mailable
{
    use Queueable;

    public function __construct(
        public readonly RentHistory $rentHistory,
        public readonly Car $car,
        public readonly Client $client,
        public readonly ?string $agreementPath = null,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Договор аренды № ' . $this->rentHistory->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.rental_completed',
            with: [
                'rentHistory' => $this->rentHistory,
                'car' => $this->car,
                'client' => $this->client,
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->agreementPath) {
            throw new Exception('Не указан путь к договору');
        }

        if (!Storage::disk('public')->exists($this->agreementPath)) {
            throw new Exception('Файл договора не найден: ' . $this->agreementPath);
        }

        return [
            Attachment::fromStorageDisk('public', $this->agreementPath)
                ->as('agreement_' . $this->rentHistory->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
