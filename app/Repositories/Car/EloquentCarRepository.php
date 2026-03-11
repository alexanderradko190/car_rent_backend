<?php

namespace App\Repositories\Car;

use App\Events\Car\CarChanged;
use App\Models\Car\Car;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;

class EloquentCarRepository implements CarRepositoryInterface
{
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function create(array $data): ?Car
    {
        $car = Car::create($data);
        event(new CarChanged($car, 'created'));

        return $car;
    }

    public function find(int $id): ?Car
    {
        return Car::with('renter')->find($id);
    }

    public function update(Car $car, array $data): ?Car
    {
        $car->update($data);
        event(new CarChanged($car, 'updated'));

        return $car;
    }

    public function delete(Car $car)
    {
        $car->forceDelete();
        event(new CarChanged($car, 'deleted'));
    }

    public function all(): Collection
    {
        return Car::with('renter')->get();
    }

    public function available(): Collection
    {
        return Car::where('status', 'available')->get();
    }
}
