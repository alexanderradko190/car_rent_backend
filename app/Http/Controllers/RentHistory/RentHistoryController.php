<?php

namespace App\Http\Controllers\RentHistory;

use App\Http\Controllers\Controller;
use App\Http\Requests\RentHistory\RentHistoryRequest;
use App\Services\RentHistory\RentHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RentHistoryController extends Controller
{
    public function __construct(
        private RentHistoryService $service
    ) {
        //
    }

    public function index(RentHistoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $filters = $request->only([
            'client_id',
            'car_id',
            'year',
            'make',
            'model'
        ]);

        $sortBy = $data['sort_by'] ?? null;
        $sortOrder = $data['sort_order'] ?? 'asc';

        $data = $this->service->filterAndSort($filters, $sortBy, $sortOrder);

        return response()->json([
            'data' => $data
        ]);
    }

    public function show($id): JsonResponse
    {
        $history = $this->service->find($id);

        if (!$history) {
            return response()->json([
                'message' => 'История не найдена'
            ], 404);
        }

        return response()->json([
            'data' => $history
        ]);
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
