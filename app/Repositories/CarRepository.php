<?php

namespace App\Repositories;

use App\Models\Car;

class CarRepository
{
    public function create(array $data): Car
    {
        return Car::create($data);
    }

    public function find(int $id): ?Car
    {
        return Car::with('renter')->find($id);
    }

    public function update(Car $car, array $data): Car
    {
        $car->update($data);

        return $car;
    }

    public function delete(Car $car): void
    {
        $car->forceDelete();
    }

    public function all()
    {
        return Car::with('renter')->get();
    }

    public function available()
    {
        return Car::where('status', 'available')->get();
    }
}
