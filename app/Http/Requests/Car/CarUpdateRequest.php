<?php

namespace App\Http\Requests\Car;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\Car\CarStatus;

class CarUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                'string',
                Rule::in(array_column(CarStatus::cases(), 'value')),
            ],
            'current_renter_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Недопустимый статус автомобиля',
            'current_renter_id.exists' => 'Арендатор не найден в системе',
        ];
    }
}
