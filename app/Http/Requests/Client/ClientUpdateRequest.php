<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiRequest;

class ClientUpdateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|string|max:32|unique:clients,phone',
            'email' => 'required|email|unique:clients,email,' . $this->route('client'),
            'driving_experience' => 'required|integer|min:0|max:80',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Телефон обязателен',
            'email.required' => 'Email обязателен',
            'email.unique' => 'Такой email уже есть',
            'driving_experience.required' => 'Опыт обязателен',
        ];
    }
}
