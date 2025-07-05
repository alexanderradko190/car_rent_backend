<?php

use App\Enums\User\UserRole;

return [
    'default' => UserRole::USER,

    'mappings' => [
        'dev7.radko@gmail.com' => UserRole::ADMIN,
        'manager@example.com' => UserRole::MANAGER,
    ],
];
