<?php

namespace App\Enums\User;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case USER = 'user';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Админ',
            self::MANAGER => 'Менеджер',
            self::USER => 'Пользователь',
        };
    }
}

