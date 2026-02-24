<?php

namespace App\Http\Requests\Auth;

use App\Enums\User\UserRole;
use App\Http\Requests\ApiRequest;

class RegisterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|string|in:' . UserRole::ADMIN->value . ',' . UserRole::MANAGER->value . ',' . UserRole::USER->value,
            'password' => 'required|string|min:6|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.min' => 'Пароль не может быть короче 6 символов',
            'password.max' => 'паролб не может быть длиннее 255 символов'
        ];
    }
}
