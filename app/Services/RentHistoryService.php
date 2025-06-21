<?php

namespace App\Services;

use App\Repositories\RentHistoryRepository;
use App\Models\RentHistory;

class RentHistoryService
{
    public function __construct(private RentHistoryRepository $repository) {}

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function filter(array $filters)
    {
        return $this->repository->filter($filters);
    }

    public function all()
    {
        return $this->repository->all();
    }
    public function find(int $id)
    {
        return $this->repository->find($id);
    }
    public function delete(RentHistory $history)
    {
        $this->repository->delete($history);
    }

    public function filterAndSort(array $filters, ?string $sortBy, string $sortOrder)
    {
        return $this->repository->filterAndSort($filters, $sortBy, $sortOrder);
    }
}
