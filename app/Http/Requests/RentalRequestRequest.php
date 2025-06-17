<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_id' => 'required|exists:cars,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'insurance_option' => 'required|boolean',
            'agreement_accepted' => 'required|accepted'
        ];
    }
}
