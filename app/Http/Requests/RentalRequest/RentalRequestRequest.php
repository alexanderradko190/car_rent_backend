<?php

namespace App\Http\Requests\RentalRequest;

use App\Http\Requests\ApiRequest;

class RentalRequestRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'car_id' => 'required|exists:cars,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'insurance_option' => 'required|boolean',
            'agreement_accepted' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'car_id.required' => 'Выберите автомобиль',
            'car_id.exists' => 'Указанный автомобиль не найден',
            'start_time.required' => 'Укажите время начала аренды',
            'start_time.date' => 'Неверный формат даты начала',
            'start_time.after' => 'Время начала должно быть в будущем',
            'end_time.required' => 'Укажите время окончания аренды',
            'end_time.date' => 'Неверный формат даты окончания',
            'end_time.after' => 'Время окончания должно быть позже начала',
            'insurance_option.required' => 'Выберите опцию страхования',
            'insurance_option.boolean' => 'Неверный формат опции страхования',
            'agreement_accepted.required' => 'Необходимо принять соглашение',
            'agreement_accepted.accepted' => 'Соглашение должно быть принято',
        ];
    }
}
