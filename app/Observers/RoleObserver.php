<?php

namespace App\Observers;

use App\Models\User\User;
use App\Enums\User\UserRole;

class RoleObserver
{
    public function saving(User $user): void
    {
        if ($user->role) {
            return;
        }

        $user->role = match ($user->email) {
            'dev7.radko@gmail.com' => UserRole::ADMIN,
            'manager@example.com' => UserRole::MANAGER,
            default => UserRole::USER,
        };
    }
}
