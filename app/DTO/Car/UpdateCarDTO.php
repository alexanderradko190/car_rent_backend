<?php
namespace App\DTO\Car;

class UpdateCarDTO
{
    public function __construct(
        public ?string $status = null,
        public ?int $current_renter_id = null
    ) {}
}
