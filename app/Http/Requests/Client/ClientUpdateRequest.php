<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiRequest;

class ClientUpdateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'string|max:32|unique:clients,phone',
            'email' => 'email|unique:clients,email',
            'driving_experience' => 'integer|min:0|max:80'
        ];
    }
}
