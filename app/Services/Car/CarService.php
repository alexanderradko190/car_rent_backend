<?php

namespace App\Services\Car;

use App\DTO\Car\CreateCarDTO;
use App\Models\Car\Car;
use App\Enums\Car\CarClass;
use App\Enums\Car\CarStatus;
use App\Models\User\User;
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
        return $this->repository->all();
    }

    public function available(): Collection
    {
        return $this->repository->available();
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

    public function update(Car $car, array $data): array
    {
        $status = $data['status'] ?? null;
        $currentRenterId = $data['current_renter_id'] ?? null;

        if (!CarStatus::tryFrom($status)) {
            return [
                'error' => 'Указан недопустимый статус автомобиля'
            ];
        }

        if ($car->status->value === $status) {
            return [
                'error' => 'Автомобиль уже арендован'
            ];
        }

        if ($currentRenterId && !User::find($currentRenterId)) {
            return [
                'error' => 'Арендатор не найден в системе'
            ];
        }

        $car = $this->repository->update($car, $data);

        if (!$car) {
            return [
                'error' => 'Не удалось обновить автомобиль'
            ];
        }

        return [
            'message' => 'Данные автомобиля обновлены',
            'data' => $car
        ];
    }

    public function delete(Car $car): array
    {
        $this->repository->delete($car);

        return [
            'message' => 'Автомобиль удалён',
            'code' => 200
        ];
    }

    public function changeStatus(Car $car, string $status): array
    {
        if (!CarStatus::tryFrom($status)) {
            return [
                'error' => 'Указан недопустимый статус автомобиля',
                'code' => 400
            ];
        }

        if ($car->status->value === $status) {
            return [
                'error' => 'Автомобиль уже арендован',
                'code' => 400
            ];
        }

        $car = $this->repository->update($car, ['status' => $status]);

        return [
            'message' => 'Статус автомобиля изменён',
            'data' => $car,
            'code' => 200
        ];
    }

    public function changeRenter(Car $car, ?int $renterId): array
    {
        $car = $this->repository->update($car, ['current_renter_id' => $renterId]);

        return [
            'message' => 'Арендатор обновлён',
            'data' => $car,
            'code' => 200
        ];
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

    public function changeCarClassAndRate(Car $car, string $carClass): array
    {
        if (!CarClass::tryFrom($carClass)) {
            return [
                'error' => 'Указан недопустимый класс автомобиля',
                'code' => 400
            ];
        }

        if ($car->car_class->value === $carClass) {
            return [
                'error' => 'Класс автомобиля уже выбран',
                'code' => 400
            ];
        }

        $rate = CarClass::from($carClass)->hourlyRate();

        $car = $this->repository->update($car, [
            'car_class' => $carClass,
            'hourly_rate' => $rate
        ]);

        return [
            'message' => 'Класс автомобиля и ставка обновлены',
            'data' => $car,
            'code' => 200
        ];
    }
}
