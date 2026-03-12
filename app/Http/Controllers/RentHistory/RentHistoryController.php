<?php

namespace App\Http\Controllers\RentHistory;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentHistoryResource;
use App\Http\Requests\RentHistory\RentHistoryRequest;
use App\Services\RentHistory\RentHistoryService;
use Illuminate\Http\JsonResponse;

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
            'data' => RentHistoryResource::collection($data) ?? null
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
            'data' => RentHistoryResource::make($history) ?? null
        ]);
    }
}
