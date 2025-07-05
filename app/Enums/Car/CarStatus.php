<?php

namespace App\Enums\Car;

enum CarStatus: string
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Доступен',
            self::RENTED => 'Арендован',
            self::MAINTENANCE => 'На обслуживании',
        };
    }
}
