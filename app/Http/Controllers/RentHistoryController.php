<?php

namespace App\Http\Controllers;

use App\Services\RentHistoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RentHistoryController extends Controller
{
    public function __construct(private RentHistoryService $service)
    {
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'sort_by'    => [
                'nullable',
                Rule::in([
                    'id', 'car_id', 'client_id', 'start_time', 'end_time', 'total_cost',
                    'car_make', 'car_model', 'client_full_name'
                ])
            ],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'client_id'  => 'nullable|integer',
            'car_id'     => 'nullable|integer',
            'year'       => 'nullable|integer',
            'make'       => 'nullable|string',
            'model'      => 'nullable|string',
        ]);

        $filters   = $request->only(['client_id', 'car_id', 'year', 'make', 'model']);
        $sortBy    = $validated['sort_by'] ?? null;
        $sortOrder = $validated['sort_order'] ?? 'asc';

        $data = $this->service->filterAndSort($filters, $sortBy, $sortOrder);

        return response()->json(['data' => $data]);
    }

    public function show($id)
    {
        $history = $this->service->find($id);
        if (!$history) {
            return response()->json(['message' => 'История не найдена'], 404);
        }
        return response()->json(['data' => $history]);
    }

    public function destroy($id)
    {
        $history = $this->service->find($id);
        if (!$history) {
            return response()->json(['message' => 'История не найдена'], 404);
        }
        $this->service->delete($history);
        return response()->json(['message' => 'История удалена']);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 1000, ",");
        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            $data = array_combine($header, $row);
            $this->service->create($data);
        }
        fclose($handle);
        return response()->json(['message' => 'Импорт завершён']);
    }

    public function export()
    {
        $histories = $this->service->all();
        $header = [
            'ID',
            'ID клиента',
            'ФИО клиента',
            'ID автомобиля',
            'Марка',
            'Модель',
            'Год выпуска',
            'VIN',
            'Гос. номер',
            'Начало аренды',
            'Окончание аренды',
            'Стоимость аренды'
        ];

        $callback = function () use ($histories, $header) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $header, ';');

            foreach ($histories as $h) {
                fputcsv($handle, [
                    $h->id,
                    $h->client_id,
                    $h->client?->full_name ?? '',
                    $h->car_id,
                    $h->car?->make ?? '',
                    $h->car?->model ?? '',
                    $h->car?->year ?? '',
                    $h->car?->vin ?? '',
                    $h->car?->license_plate ?? '',
                    $h->start_time,
                    $h->end_time,
                    $h->total_cost,
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rent_histories.csv"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
