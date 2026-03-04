<?php

namespace App\Http\Controllers\Car;

use App\DTO\Car\CreateCarDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Car\CarChangeClassRequest;
use App\Http\Requests\Car\CarChangeLicensePlateRequest;
use App\Http\Requests\Car\CarChangeRenterRequest;
use App\Http\Requests\Car\CarChangeStatusRequest;
use App\Http\Requests\Car\CarCreateRequest;
use App\Http\Requests\Car\CarUpdateRequest;
use App\Models\Car\Car;
use App\Services\Car\CarService;
use Illuminate\Http\JsonResponse;

class CarController extends Controller
{
    public function __construct(
        private CarService $service
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $cars = $this->service->getAll();

        return response()->json([
            'message' => 'Список автомобилей',
            'data' => $cars
        ]);
    }

    public function show($id): JsonResponse
    {
        $car = $this->service->find($id);

        if (!$car) {
            return response()->json([
                'message' => 'Автомобиль не найден'],
                404);
        }

        return response()->json([
            'data' => $car
        ]);
    }

    public function store(CarCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dto = new CreateCarDTO(
            $data['make'],
            $data['model'],
            $data['year'],
            $data['vin'],
            $data['license_plate'],
            $data['power'],
            $data['car_class'],
            $data['hourly_rate']
        );

        $car = $this->service->create($dto);

        return response()->json([
            'message' => 'Автомобиль создан',
            'data' => $car,
        ], 201);
    }

    public function update(CarUpdateRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        $car = $this->service->find($id);

        if (!$car) {
            return response()->json(['message' => 'Автомобиль не найден'], 404);
        }

        $updatedCar = $this->service->update($car, $data);

        if (isset($updatedCar['error'])) {
            return response()->json([
                'message' => $updatedCar['error']
            ], 400);
        }

        return response()->json([
            'message' => 'Автомобиль успешно обновлен',
            'data' => $updatedCar
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $car = $this->service->find($id);

        if (!$car) {
            return response()->json([
                'message' => 'Автомобиль не найден'
            ], 404);
        }

        $this->service->delete($car);

        return response()->json([
            'message' => 'Автомобиль успешно удален'
        ], 204);
    }

    public function available(): JsonResponse
    {
        $availableCars = $this->service->available();

        return response()->json([
            'message' => 'Доступные автомобили',
            'data' => $availableCars
        ]);
    }

    public function changeStatus(CarChangeStatusRequest $request, Car $car): JsonResponse
    {
        $data = $request->validated();

        $this->service->changeStatus($car, $data['status']);

        return response()->json([
            'message' => 'Статус автомобиля обновлен'
        ], 201);
    }

    public function changeLicensePlate(CarChangeLicensePlateRequest $request, Car $car): JsonResponse
    {
        $data = $request->validated();

        $this->service->changeLicensePlate($car, $data['license_plate']);

        return response()->json([
            'message' => 'Номер автомобиля обновлен'
        ], 201);
    }

    public function changeRenter(CarChangeRenterRequest $request, Car $car): JsonResponse
    {
        $data = $request->validated();

        $this->service->changeRenter($car, $data['current_renter_id']);

        return response()->json([
            'message' => 'Арендатор автомобиля обновлен'
        ], 201);
    }

    public function changeCarClassAndRate(CarChangeClassRequest $request, Car $car): JsonResponse
    {
        $data = $request->validated();

        $this->service->changeCarClassAndRate($car, $data['car_class']);

        return response()->json([
            'message' => 'Класс автомобиля обновлен'
        ], 201);
    }
}
