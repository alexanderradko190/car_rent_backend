<?php

namespace App\Http\Requests\Auth;

use App\Enums\User\UserRole;
use App\Http\Requests\ApiRequest;

class LoginRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required|string|min:6|max:255'
        ];
    }
}
