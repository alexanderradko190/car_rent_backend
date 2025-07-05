<?php

namespace App\Http\Requests\Car;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Car\CarStatus;
use Illuminate\Validation\Rule;

class CarChangeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(array_column(CarStatus::cases(), 'value')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Укажите статус автомобиля',
            'status.in' => 'Недопустимый статус автомобиля',
        ];
    }
}
