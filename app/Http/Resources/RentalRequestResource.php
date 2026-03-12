<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalRequestResource extends JsonResource
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
            'client_id' => $this->client_id,
            'car_id' => $this->car_id,
            'start_time' => $this->start_time?->toDateTimeString(),
            'end_time' => $this->end_time?->toDateTimeString(),
            'total_cost' => $this->total_cost,
            'status' => $this->status?->value ?? $this->status,
            'agreement_path' => $this->agreement_path,
            'client' => ClientResource::make($this->whenLoaded('client')),
            'car' => CarResource::make($this->whenLoaded('car'))
        ];
    }
}
