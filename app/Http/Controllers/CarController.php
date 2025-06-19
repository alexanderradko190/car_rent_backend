<?php

namespace App\Http\Controllers;

use App\DTO\Car\CreateCarDTO;
use App\Http\Requests\CarCreateRequest;
use App\Models\Car;
use App\Services\CarService;
use App\Http\Requests\CarUpdateRequest;
use App\Http\Requests\CarChangeStatusRequest;
use App\Http\Requests\CarChangeRenterRequest;
use App\Http\Requests\CarChangeClassRequest;
use App\Http\Requests\CarChangeLicensePlateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function __construct(private CarService $service){}

    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Список автомобилей',
            'data' => $this->service->getAll(),
        ]);
    }

    public function show($id): JsonResponse
    {
        $car = $this->service->find($id);

        if (!$car) {
            return response()->json(['message' => 'Автомобиль не найден'], 404);
        }

        return response()->json([
            'message' => 'Данные автомобиля',
            'data' => $car,
        ]);
    }

    public function store(CarCreateRequest $request): JsonResponse
    {
        $dto = new CreateCarDTO(...$request->validated());

        return response()->json($this->service->create($dto), 201);
    }

    public function update(CarUpdateRequest $request, $id): JsonResponse
    {
        $car = $this->service->find($id);

        if (!$car) {
            return response()->json(['message' => 'Автомобиль не найден'], 404);
        }

        $result = $this->service->update($car, $request->validated());

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function destroy($id): JsonResponse
    {
        $car = $this->service->find($id);

        if (!$car) {
            return response()->json(['message' => 'Автомобиль не найден'], 404);
        }
        $result = $this->service->delete($car);

        return response()->json(['message' => $result['message']], $result['code']);
    }

    public function available(): JsonResponse
    {
        return response()->json([
            'message' => 'Доступные автомобили',
            'data' => $this->service->available(),
        ]);
    }

    public function changeStatus(Request $request, Car $car): JsonResponse
    {
        return response()->json($this->service->changeStatus($car, $request->input('status')));
    }

    public function changeLicensePlate(Request $request, Car $car): JsonResponse
    {
        return response()->json($this->service->changeLicensePlate($car, $request->input('license_plate')));
    }

    public function changeRenter(Request $request, Car $car): JsonResponse
    {
        return response()->json($this->service->changeRenter($car, $request->input('current_renter_id')));
    }

    public function changeCarClassAndRate(Request $request, Car $car): JsonResponse
    {
        return response()->json($this->service->changeCarClassAndRate($car, $request->input('car_class')));
    }
}
