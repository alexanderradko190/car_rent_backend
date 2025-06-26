<?php

namespace App\Http\Controllers;

use App\Services\RentHistoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;

class RentHistoryController extends Controller
{
    public function __construct(private RentHistoryService $service)
    {
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'sort_by' => [
                'nullable',
                Rule::in([
                    'id', 'car_id', 'client_id', 'start_time', 'end_time', 'total_cost',
                    'car_make', 'car_model', 'client_full_name'
                ])
            ],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'client_id' => 'nullable|integer',
            'car_id' => 'nullable|integer',
            'year' => 'nullable|integer',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
        ]);

        $filters = $request->only(['client_id', 'car_id', 'year', 'make', 'model']);
        $sortBy = $validated['sort_by'] ?? null;
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

    public function export(Request $request)
    {
        $userId = auth()->id();
        $params = [
            'user_id' => $userId,
        ];

        $response = Http::post(env('GO_EXPORT_URL') . '/api/export', $params);

        if ($response->failed()) {
            return response()->json(['error' => 'Ошибка при создании экспорта'], 500);
        }

        $data = $response->json();
        return response()->json([
            'task_id' => $data['task_id'],
            'status' => $data['status'],
        ]);
    }

    public function exportStatus(Request $request, $taskId)
    {
        $response = Http::get(env('GO_EXPORT_URL') . "/api/export/$taskId/status");

        if ($response->failed()) {
            return response()->json(['error' => 'Ошибка получения статуса'], 500);
        }
        return response()->json($response->json());
    }

    public function exportDownload(Request $request, $taskId)
    {
        $response = Http::get(env('GO_EXPORT_URL') . "/api/export/$taskId/download");

        if ($response->failed()) {
            return response()->json(['error' => 'Файл не найден'], 404);
        }
        $data = $response->json();
        return response()->json($data);
    }
}
