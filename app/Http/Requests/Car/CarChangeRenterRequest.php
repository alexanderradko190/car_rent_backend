<?php

namespace App\Http\Requests\Car;

use App\Http\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class CarChangeRenterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'current_renter_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_renter_id.exists' => 'Арендатор не найден в системе',
        ];
    }
}
