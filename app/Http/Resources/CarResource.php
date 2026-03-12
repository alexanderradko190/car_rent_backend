<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'vin' => $this->vin,
            'license_plate' => $this->license_plate,
            'car_class' => $this->car_class?->value ?? $this->car_class,
            'status' => $this->status?->value,
            'current_renter_id' => $this->current_renter_id,
            'renter' => ClientResource::make($this->whenLoaded('renter'))
        ];
    }
}
