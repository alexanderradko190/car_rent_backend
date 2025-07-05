<?php

namespace App\Repositories\RentHistory;

use App\Models\RentHistory\RentHistory;

class RentHistoryRepository
{
    public function create(array $data): RentHistory
    {
        return RentHistory::create($data);
    }

    public function all()
    {
        return RentHistory::with(['car', 'client'])->get();
    }

    public function find(int $id): ?RentHistory
    {
        return RentHistory::find($id);
    }

    public function delete(RentHistory $history): void
    {
        $history->delete();
    }

    public function filterAndSort(array $filters, ?string $sortBy, string $sortOrder)
    {
        $query = RentHistory::with(['car', 'client']);

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        if (!empty($filters['car_id'])) {
            $query->where('car_id', $filters['car_id']);
        }
        if (!empty($filters['year'])) {
            $query->whereHas('car', function ($q) use ($filters) {
                $q->where('year', $filters['year']);
            });
        }
        if (!empty($filters['make'])) {
            $query->whereHas('car', function ($q) use ($filters) {
                $q->where('make', $filters['make']);
            });
        }
        if (!empty($filters['model'])) {
            $query->whereHas('car', function ($q) use ($filters) {
                $q->where('model', $filters['model']);
            });
        }

        $allowedSortFields = [
            'id', 'car_id', 'client_id', 'start_time', 'end_time', 'total_cost',
        ];

        if ($sortBy && in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } elseif ($sortBy === 'car_make') {
            $query->join('cars', 'rent_histories.car_id', '=', 'cars.id')
                ->orderBy('cars.make', $sortOrder)
                ->select('rent_histories.*');
        } elseif ($sortBy === 'car_model') {
            $query->join('cars', 'rent_histories.car_id', '=', 'cars.id')
                ->orderBy('cars.model', $sortOrder)
                ->select('rent_histories.*');
        } elseif ($sortBy === 'client_full_name') {
            $query->join('clients', 'rent_histories.client_id', '=', 'clients.id')
                ->orderBy('clients.full_name', $sortOrder)
                ->select('rent_histories.*');
        }

        return $query->get();
    }
}
