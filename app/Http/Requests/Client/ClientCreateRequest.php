<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiRequest;

class ClientCreateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'age' => 'required|integer|min:18|max:100',
            'phone' => 'required|string|max:32|unique:clients,phone',
            'driving_experience' => 'nullable|integer|min:0|max:80',
            'license_scan' => 'nullable|file|mimes:pdf,jpeg,png|max:10240',
        ];
    }
}
