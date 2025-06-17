<?php
namespace App\DTO\Car;

class CreateCarDTO
{
    public function __construct(
        public string $make,
        public string $model,
        public int $year,
        public string $vin,
        public string $license_plate,
        public int $power,
        public string $car_class,
        public float $hourly_rate
    ) {}
}
