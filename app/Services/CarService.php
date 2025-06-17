<?php

namespace App\Services;

use App\DTO\Car\CreateCarDTO;
use App\DTO\Car\UpdateCarDTO;
use App\Repositories\CarRepository;
use App\Enums\CarClass;
use App\Models\Car;

class CarService
{
    public function __construct(private CarRepository $repository)
    {
    }

    public function create(CreateCarDTO $dto)
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

    public function update(Car $car, UpdateCarDTO $dto)
    {
        return $this->repository->update(
            $car,
            array_filter([
                'status' => $dto->status,
                'current_renter_id' => $dto->current_renter_id,
            ])
        );
    }

    public function changeLicensePlate(Car $car, string $licensePlate)
    {
        return $this->repository->update(
            $car,
            [
                'license_plate' => $licensePlate
            ]
        );
    }

    public function changeStatus(Car $car, string $status)
    {
        return $this->repository->update(
            $car,
            [
                'status' => $status
            ]
        );
    }

    public function changeRenter(Car $car, ?int $renterId)
    {
        return $this->repository->update(
            $car,
            [
                'current_renter_id' => $renterId
            ]
        );
    }

    public function changeCarClassAndRate(Car $car, string $carClass)
    {
        $rate = CarClass::from($carClass)->hourlyRate();

        return $this->repository->update($car,
            [
                'car_class' => $carClass,
                'hourly_rate' => $rate
            ]
        );
    }

    public function delete(Car $car)
    {
        $this->repository->delete($car);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function available()
    {
        return $this->repository->available();
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }
}
