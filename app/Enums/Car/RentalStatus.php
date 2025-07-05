<?php

namespace App\Enums\Car;

enum RentalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'На рассмотрении',
            self::APPROVED => 'Подтверждена',
            self::REJECTED => 'Отклонена',
            self::COMPLETED => 'Завершена',
        };
    }
}
