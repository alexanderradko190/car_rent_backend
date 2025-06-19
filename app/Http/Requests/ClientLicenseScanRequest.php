<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientLicenseScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license_scan' => 'required|file|mimes:pdf,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'license_scan.required' => 'Файл обязателен',
            'license_scan.mimes' => 'Только pdf, jpeg, png',
            'license_scan.max' => 'Максимальный размер файла 10 Мб',
        ];
    }
}
