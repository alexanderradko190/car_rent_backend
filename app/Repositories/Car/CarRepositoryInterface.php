<?php

namespace App\Repositories\Car;

use App\Models\Car\Car;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;

interface CarRepositoryInterface
{

    public function create(array $data): ?Car;

    public function find(int $id): ?Car;

    public function update(Car $car, array $data): ?Car;

    public function delete(Car $car);

    public function all(): Collection;

    public function available();
}
