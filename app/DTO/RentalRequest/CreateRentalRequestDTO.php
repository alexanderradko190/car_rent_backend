<?php

namespace App\DTO\RentalRequest;

class CreateRentalRequestDTO
{
    public function __construct(
        public int $client_id,
        public int $car_id,
        public string $start_time,
        public string $end_time,
        public bool $insurance_option,
        public bool $agreement_accepted
    ) {}
}
