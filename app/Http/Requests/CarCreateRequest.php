<?php

namespace App\Http\Requests;

use App\Enums\CarClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CarCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:'.(date('Y')+1),
            'vin' => [
                'required',
                'string',
                'unique:cars',
                'regex:/^[A-HJ-NPR-Z0-9]{17}$/'
            ],
            'license_plate' => 'required|string|unique:cars|max:15',
            'car_class' => ['required', new Enum(CarClass::class)],
            'power' => 'required|integer|min:50|max:1000',
            'hourly_rate' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'vin.regex' => 'VIN должен состоять из 17 заглавных букв и цифр',
            'year.max' => 'Год выпуска не может быть в будущем',
        ];
    }
}
