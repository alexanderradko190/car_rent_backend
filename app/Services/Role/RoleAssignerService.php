<?php

namespace App\Services\Role;

use App\Models\User\User;
use App\Enums\User\UserRole;

class RoleAssignerService
{
    public function assign(User $user): void
    {
        if (!$user->role || $user->role === UserRole::USER) {
            $user->role = UserRole::USER;
        }
    }
}
