<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentHistoryResource extends JsonResource
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
            'car_id' => $this->car_id,
            'client_id' => $this->client_id,
            'start_time' => $this->start_time?->toDateTimeString(),
            'end_time' => $this->end_time?->toDateTimeString(),
            'total_cost' => $this->total_cost
        ];
    }
}
