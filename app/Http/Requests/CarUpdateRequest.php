<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|string',
            'current_renter_id' => 'nullable|exists:users,id',
            'hourly_rate' => 'sometimes|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'hourly_rate.numeric' => 'Стоимость аренды должна быть числом',
            'hourly_rate.min' => 'Стоимость аренды должна быть больше 0',
            'status.*' => 'Недопустимый статус автомобиля',
            'current_renter_id.exists' => 'Указанный арендатор не найден в системе'
        ];
    }
}
