<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\User;
use App\Enums\User\UserRole;

class UserRolesSeeder extends Seeder
{
//    Сначала создаем пользователя, потом запускаем php artisan db:seed --class=UserRolesSeeder
    public function run(): void
    {
        User::where('email', 'dev7.radko@gmail.com')->update(['role' => UserRole::ADMIN]);
        User::where('email', 'manager@example.com')->update(['role' => UserRole::MANAGER]);
    }
}
