<?php

namespace Database\Seeders;

use App\Enums\User\UserRole;
use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AddDefaultUsersSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'secret';

    public function run(): void
    {
        $users = [
            [
                'name' => 'Администратор',
                'email' => 'admin@example.com',
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'role' => UserRole::ADMIN->value,
            ],
            [
                'name' => 'Менеджер',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::MANAGER->value,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
