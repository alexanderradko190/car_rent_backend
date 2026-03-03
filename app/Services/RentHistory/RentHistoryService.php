<?php

namespace App\Services\RentHistory;

use App\Models\RentHistory\RentHistory;
use App\Repositories\RentHistory\RentHistoryRepository;
use Illuminate\Database\Eloquent\Collection;

class RentHistoryService
{
    public function __construct(private RentHistoryRepository $repository)
    {
        //
    }

    public function create(array $data): RentHistory
    {
        return $this->repository->create($data);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): ?RentHistory
    {
        return $this->repository->find($id);
    }

    public function filterAndSort(array $filters, ?string $sortBy, string $sortOrder): Collection
    {
        return $this->repository->filterAndSort($filters, $sortBy, $sortOrder);
    }
}
