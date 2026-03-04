<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class ReportGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:rent_histories,rental_requests',
            'date_from' => 'required|date',
            'date_to' => 'required|date'
        ];
    }
}
