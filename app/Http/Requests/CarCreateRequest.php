<?php

namespace App\Http\Requests;

use App\Enums\CarClass;
use Illuminate\Validation\Rules\Enum;

class CarCreateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => ['required', 'string', 'unique:cars,vin', 'regex:/^[A-HJ-NPR-Z0-9]{17}$/'],
            'license_plate' => 'required|string|unique:cars,license_plate|max:15',
            'car_class' => ['required', new Enum(CarClass::class)],
            'power' => 'required|integer|min:50|max:1000',
            'hourly_rate' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'make.required' => 'Марка автомобиля — обязательное поле',
            'model.required' => 'Модель автомобиля — обязательное поле',
            'year.required' => 'Укажите год выпуска автомобиля',
            'year.min' => 'Год выпуска не может быть раньше 1900',
            'year.max' => 'Год выпуска не может быть в будущем',
            'vin.required' => 'VIN — обязательное поле',
            'vin.unique' => 'Автомобиль с таким VIN уже существует',
            'vin.regex' => 'VIN должен состоять из 17 заглавных букв и цифр',
            'license_plate.required' => 'Номерной знак — обязательное поле',
            'license_plate.unique' => 'Автомобиль с таким номерным знаком уже существует',
            'license_plate.max' => 'Номерной знак не может быть длиннее 15 символов',
            'car_class.required' => 'Класс автомобиля — обязательное поле',
            'power.required' => 'Укажите мощность автомобиля',
            'power.min' => 'Мощность должна быть не менее 50 л.с.',
            'power.max' => 'Мощность должна быть не более 1000 л.с.',
            'hourly_rate.required' => 'Укажите почасовую ставку',
            'hourly_rate.numeric' => 'Стоимость аренды должна быть числом',
            'hourly_rate.min' => 'Стоимость аренды должна быть больше 0',
        ];
    }
}
