<?php

namespace App\Http\Requests\Car;

use App\Http\Requests\ApiRequest;

class CarChangeLicensePlateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'license_plate' => 'required|string|unique:cars,license_plate|max:15',
        ];
    }

    public function messages(): array
    {
        return [
            'license_plate.required' => 'Номерной знак обязателен',
            'license_plate.unique' => 'Автомобиль с таким номерным знаком уже существует',
            'license_plate.max' => 'Номерной знак не может быть длиннее 15 символов',
        ];
    }
}
