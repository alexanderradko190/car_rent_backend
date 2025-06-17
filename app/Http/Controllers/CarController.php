<?php
namespace App\Http\Controllers;

use App\DTO\Car\CreateCarDTO;
use App\DTO\Car\UpdateCarDTO;
use App\Http\Requests\CarCreateRequest;
use App\Http\Requests\CarUpdateRequest;
use App\Models\Car;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function __construct(private CarService $service) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function show(Car $car): JsonResponse
    {
        return response()->json($car->load('renter'));
    }

    public function store(CarCreateRequest $request): JsonResponse
    {
        $dto = new CreateCarDTO(...$request->validated());
        return response()->json($this->service->create($dto), 201);
    }

    public function update(CarUpdateRequest $request, Car $car): JsonResponse
    {
        $dto = new UpdateCarDTO(...$request->validated());

        return response()->json($this->service->update($car, $dto));
    }

    public function destroy(Car $car): JsonResponse
    {
        $this->service->delete($car);

        return response()->json(null, 204);
    }

    public function available(): JsonResponse
    {
        return response()->json($this->service->available());
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
