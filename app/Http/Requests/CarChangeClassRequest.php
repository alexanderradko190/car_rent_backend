<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\CarClass;
use Illuminate\Validation\Rule;

class CarChangeClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_class' => [
                'required',
                'string',
                Rule::in(array_column(CarClass::cases(), 'value')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'car_class.required' => 'Класс автомобиля — обязательное поле',
            'car_class.in' => 'Недопустимый класс автомобиля',
        ];
    }
}
