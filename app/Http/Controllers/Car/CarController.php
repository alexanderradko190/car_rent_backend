<?php

namespace App\Http\Controllers\Car;

use App\DTO\Car\CreateCarDTO;
use App\Enums\Car\CarStatus;
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
    public function __construct(private CarService $service) {}

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

        return response()->json([
            'message' => 'Автомобиль успешно обновлен',
            'data' => $updatedCar,
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

        $clientId = auth()->id();

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

    public function export()
    {
        $cars = $this->service->getAll();
        $header = [
            'ID',
            'Марка',
            'Модель',
            'Год',
            'VIN',
            'Гос. номер',
            'Статус',
            'Мощность',
            'Класс',
            'Тариф (руб/час)',
            'Дата создания',
            'Дата изменения',
        ];

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, $header, ';');

        foreach ($cars as $car) {
            $row = [
                $car->id,
                $car->make,
                $car->model,
                $car->year,
                $car->vin,
                $car->license_plate,
                $car->status instanceof CarStatus ? $car->status->label() : $car->status,
                $car->power,
                $car->car_class?->label() ?? $car->car_class,
                $car->hourly_rate,
                $car->created_at,
                $car->updated_at,
            ];
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="cars.csv"');
    }
}
