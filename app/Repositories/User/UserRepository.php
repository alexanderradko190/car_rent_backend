<?php

namespace App\Repositories\User;

use App\Models\User\User;

class UserRepository
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }
}
