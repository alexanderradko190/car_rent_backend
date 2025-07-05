<?php

namespace App\Enums\Car;

enum CarClass: string
{
    case ECONOMY = 'economy';
    case COMFORT = 'comfort';
    case BUSINESS = 'business';

    public function label(): string
    {
        return match($this) {
            self::ECONOMY => 'Эконом',
            self::COMFORT => 'Комфорт',
            self::BUSINESS => 'Бизнес',
        };
    }

    public function hourlyRate(): float
    {
        return match($this) {
            self::ECONOMY => 500,
            self::COMFORT => 1000,
            self::BUSINESS => 1500,
        };
    }
}
