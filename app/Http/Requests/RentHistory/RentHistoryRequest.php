<?php

namespace App\Http\Requests\RentHistory;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class RentHistoryRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'sort_by' => [
                'nullable',
                Rule::in([
                    'id', 'car_id', 'client_id', 'start_time', 'end_time', 'total_cost',
                    'car_make', 'car_model', 'client_full_name'
                ])
            ],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'client_id' => 'nullable|integer',
            'car_id' => 'nullable|integer',
            'year' => 'nullable|integer',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
        ];
    }
}
