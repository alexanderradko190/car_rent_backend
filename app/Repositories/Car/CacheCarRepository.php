<?php

namespace App\Repositories\Car;

use App\Models\Car\Car;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;



class CacheCarRepository implements CarRepositoryInterface
{
    private CarRepositoryInterface $repository;
    private Cache $cache;
    private bool $cacheIsOn;

    public function __construct(CarRepositoryInterface $repository, Cache $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->cacheIsOn = config('cache.on');
    }

    public function create(array $data): ?Car
    {
        $result = $this->repository->create($data);

        return $result;
    }

    public function find(int $id): ?Car
    {
        return $this->repository->find($id);
    }

    public function update(Car $car, array $data): ?Car
    {
        $result = $this->repository->update($car, $data);

        return $result;
    }

    public function delete(Car $car)
    {
       return $this->repository->delete($car);
    }

    public function all(): Collection
    {
        if (!$this->cacheIsOn) {
            return collect($this->repository->all());
        }

        $cacheKey = 'cars_all';

        $rows = $this->cache->rememberForever($cacheKey, function () {
            return collect($this->repository->all())->toArray();
        });

        return collect($rows);
    }

    public function available(): Collection
    {
        return $this->all()
            ->where('status', 'available')
            ->whereNull('current_renter_id')
            ->values();
    }
}
