<?php

namespace App\Repositories\User;

use App\Models\User\User;

class UserRepository implements UsersRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }
}
