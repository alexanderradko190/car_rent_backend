<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'age' => $this->age,
            'phone' => $this->phone,
            'email' => $this->email,
            'driving_experience' => $this->driving_experience,
            'license_scan_path' => $this->license_scan
        ];
    }
}
