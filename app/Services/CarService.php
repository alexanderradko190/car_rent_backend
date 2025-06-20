<?php

namespace App\Services;

use App\DTO\Car\CreateCarDTO;
use App\Repositories\CarRepository;
use App\Models\Car;
use App\Enums\CarClass;
use App\Enums\CarStatus;
use App\Models\User;

class CarService
{
    public function __construct(private CarRepository $repository){}

    public function find(int $id): ?Car
    {
        return $this->repository->find($id);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function available()
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
        $updated = false;
        $messages = [];

        if (isset($data['status'])) {
            if (!CarStatus::tryFrom($data['status'])) {
                return ['error' => 'Указан недопустимый статус автомобиля', 'code' => 400];
            }

            if ($car->status->value === $data['status']) {
                return ['error' => 'Автомобиль уже находится в данном статусе', 'code' => 400];
            }

            $updated = true;
        }
        if (array_key_exists('current_renter_id', $data)) {
            if ($data['current_renter_id'] !== null && !User::find($data['current_renter_id'])) {
                return ['error' => 'Арендатор не найден в системе', 'code' => 400];

            }
//            if ($car->current_renter_id === $data['current_renter_id']) {
//                return ['error' => 'Этот пользователь уже привязан к автомобилю', 'code' => 400];
//            }
            $updated = true;
        }

        if (!$updated) {
            return ['error' => 'Нет параметров для обновления', 'code' => 400];
        }

        $car = $this->repository->update($car, $data);

        return [
            'message' => 'Данные автомобиля обновлены',
            'data' => $car,
            'code' => 200,
        ];
    }

    public function delete(Car $car): array
    {
        $this->repository->delete($car);

        return ['message' => 'Автомобиль удалён', 'code' => 200];
    }

    public function changeStatus(Car $car, string $status): array
    {
        if (!CarStatus::tryFrom($status)) {
            return ['error' => 'Указан недопустимый статус автомобиля', 'code' => 400];
        }

        if ($car->status->value === $status) {
            return ['error' => 'Автомобиль уже находится в данном статусе', 'code' => 400];
        }

        $car = $this->repository->update($car, ['status' => $status]);

        return [
            'message' => 'Статус автомобиля изменён',
            'data' => $car,
            'code' => 200,
        ];
    }

    public function changeRenter(Car $car, ?int $renterId): array
    {

//        if ($car->current_renter_id === $renterId) {
//            return ['error' => 'Этот пользователь уже привязан к автомобилю', 'code' => 400];
//        }

        if ($renterId !== null && !User::find($renterId)) {
            return ['error' => 'Арендатор не найден в системе', 'code' => 400];
        }
        $car = $this->repository->update($car, ['current_renter_id' => $renterId]);

        return [
            'message' => 'Арендатор обновлён',
            'data' => $car,
            'code' => 200,
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
            return ['error' => 'Указан недопустимый класс автомобиля', 'code' => 400];
        }

        if ($car->car_class->value === $carClass) {
            return ['error' => 'Класс автомобиля уже выбран', 'code' => 400];
        }

        $rate = CarClass::from($carClass)->hourlyRate();

        $car = $this->repository->update($car, [
            'car_class' => $carClass,
            'hourly_rate' => $rate,
        ]);

        return [
            'message' => 'Класс автомобиля и ставка обновлены',
            'data' => $car,
            'code' => 200,
        ];
    }
}
