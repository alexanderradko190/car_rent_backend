<?php

namespace App\Repositories;

use App\Models\RentalRequest;

class RentalRequestRepository
{
    public function create(array $data): RentalRequest
    {
        return RentalRequest::create($data);
    }

    public function find(int $id): ?RentalRequest
    {
        return RentalRequest::find($id);
    }

    public function withClient(int $id): ?RentalRequest
    {
        return RentalRequest::with('client')->find($id);
    }

    public function withCar(int $id): ?RentalRequest
    {
        return RentalRequest::with('car')->find($id);
    }

    public function all()
    {
        return RentalRequest::with(['car', 'client'])->get();
    }

    public function update(RentalRequest $request, array $data): RentalRequest
    {
        $request->update($data);
        return $request;
    }

    public function delete(RentalRequest $request): void
    {
        $request->delete();
    }

    public function intersecting($car_id, $start, $end)
    {
        return RentalRequest::where('car_id', $car_id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_time', '<=', $start)
                            ->where('end_time', '>=', $end);
                    });
            })->exists();
    }
}
