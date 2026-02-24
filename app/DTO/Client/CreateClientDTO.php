<?php

namespace App\DTO\Client;

class CreateClientDTO
{
    public function __construct(
        public int $user_id,
        public string $full_name,
        public int $age,
        public string $phone,
        public string $email,
        public ?int $driving_experience = null,
        public ?string $license_scan = null,
    ) {}
}
