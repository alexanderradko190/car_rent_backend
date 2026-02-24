<?php

namespace App\Repositories\User;

use App\Models\User\User;

interface UsersRepositoryInterface
{
    public function find(int $id): ?User;
}
