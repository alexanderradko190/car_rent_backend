<?php

namespace App\Services\Car;

use App\DTO\Car\CreateCarDTO;
use App\Models\Car\Car;
use App\Enums\Car\CarClass;
use App\Enums\Car\CarStatus;
use App\Exceptions\ServiceException;
use App\Models\Client\Client;
use App\Repositories\Car\CarRepositoryInterface;
use Illuminate\Support\Collection;

class CarService
{
    public function __construct(
        private CarRepositoryInterface $repository
    ) {
        //
    }

    public function find(int $id): ?Car
    {
        return $this->repository->find($id);
    }

    public function getAll(): Collection
    {
        return $this->normalizeCollection($this->repository->all());
    }

    public function available(): Collection
    {
        return $this->normalizeCollection($this->repository->available());
    }

    public function create(CreateCarDTO $dto): ?Car
    {
        return $this->repository->create([
            'make' => $dto->make,
            'model' => $dto->model,
            'year' => $dto->year,
            'vin' => $dto->vin,
            'license_plate' => $dto->license_plate,
            'power' => $dto->power,
            'car_class' => $dto->car_class,
            'hourly_rate' => $dto->hourly_rate,
            'status' => 'available',
        ]);
    }

    public function update(Car $car, array $data): Car
    {
        $statusProvided = array_key_exists('status', $data);
        $status = $data['status'] ?? null;
        $currentRenterId = $data['current_renter_id'] ?? null;

        if ($statusProvided) {
            if (!CarStatus::tryFrom($status)) {
                throw new ServiceException('Указан недопустимый статус автомобиля', 400);
            }

            if ($car->status->value === $status) {
                throw new ServiceException('Автомобиль уже арендован', 400);
            }
        }

        if ($currentRenterId && !Client::find($currentRenterId)) {
            throw new ServiceException('Арендатор не найден в системе', 404);
        }

        $car = $this->repository->update($car, $data);

        if (!$car) {
            throw new ServiceException('Не удалось обновить автомобиль', 500);
        }

        return $car;
    }

    public function delete(Car $car): void
    {
        $this->repository->delete($car);
    }

    public function changeStatus(Car $car, string $status): Car
    {
        if (!CarStatus::tryFrom($status)) {
            throw new ServiceException('Указан недопустимый статус автомобиля', 400);
        }

        if ($car->status->value === $status) {
            throw new ServiceException('Автомобиль уже арендован', 400);
        }

        $car = $this->repository->update($car, ['status' => $status]);

        return $car;
    }

    public function changeRenter(Car $car, ?int $renterId): ?Car
    {
        $car = $this->repository->update($car, ['current_renter_id' => $renterId]);

        return $car;
    }

    public function changeLicensePlate(Car $car, string $licensePlate): ?Car
    {
        return $this->repository->update(
            $car,
            [
                'license_plate' => $licensePlate
            ]
        );
    }

    public function changeCarClassAndRate(Car $car, string $carClass): Car
    {
        if (!CarClass::tryFrom($carClass)) {
            throw new ServiceException('Указан недопустимый класс автомобиля', 400);
        }

        if ($car->car_class->value === $carClass) {
            throw new ServiceException('Класс автомобиля уже выбран', 400);
        }

        $rate = CarClass::from($carClass)->hourlyRate();

        $car = $this->repository->update($car, [
            'car_class' => $carClass,
            'hourly_rate' => $rate
        ]);

        return $car;
    }

    private function normalizeCollection(Collection $cars): Collection
    {
        if ($cars->isEmpty()) {
            return $cars;
        }

        $first = $cars->first();
        if (is_array($first)) {
            return Car::hydrate($cars->all());
        }

        return $cars;
    }
}
