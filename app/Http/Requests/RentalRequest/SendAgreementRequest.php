<?php

namespace App\Http\Requests\RentalRequest;

use App\Http\Requests\ApiRequest;

class SendAgreementRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'rent_history_id' => 'nullable|integer|exists:rent_histories,id',
            'force' => 'sometimes|boolean',
        ];
    }
}
