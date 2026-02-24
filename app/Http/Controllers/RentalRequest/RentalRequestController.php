<?php

namespace App\Http\Controllers\RentalRequest;

use App\DTO\RentalRequest\CreateRentalRequestDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RentalRequest\RentalRequestRequest;
use App\Services\RentalRequest\RentalRequestService;
use Illuminate\Http\JsonResponse;

class RentalRequestController extends Controller
{
    public function __construct(private RentalRequestService $service)
    {
        //
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->service->all()
        ]);
    }

    public function show($id): JsonResponse
    {
        $request = $this->service->find($id);

        if (!$request) {
            return response()->json([
                'message' => 'Заявка не найдена'
            ], 404);
        }

        return response()->json([
            'data' => $request
        ]);
    }

    public function store(RentalRequestRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['client_id'] = auth()->id();

        $dto = new CreateRentalRequestDTO(...$validated);

        $result = $this->service->create($dto);

        if (!$result) {
            return response()->json([
                'message' => 'Не удалось создать заявку на аренду'
            ], 400);
        }

        return response()->json([
            'message' => 'Заявка на аренду создана'
        ], 201);
    }

    public function approve($id): JsonResponse
    {
        $result = $this->service->approve($id);

        if (!$result) {
            return response()->json([
                'message' => 'Не удалось подтвердить заявку на аренду'
            ], 400);
        }

        return response()->json([
            'message' => 'Заявка на аренду подтверждена'
        ], 201);
    }

    public function reject($id): JsonResponse
    {
        $result = $this->service->reject($id);

        if (!$result) {
            return response()->json([
                'message' => 'Не удалось отклонить заявку на аренду'
            ], 400);
        }

        return response()->json([
            'message' => 'Заявка на аренду отклонена'
        ], 201);
    }

    public function complete($id): JsonResponse
    {
        $result = $this->service->complete($id);

        if (!$result) {
            return response()->json([
                'message' => 'Не удалось завершить аренду'
            ], 400);
        }

        return response()->json([
            'message' => 'Аренда завершена'
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->service->delete($id);

        if (!$result) {
            return response()->json([
                'message' => 'Не удалось удалить заявку на аренду'
            ], 400);
        }

        return response()->json([
            'message' => 'Заявка на аренду удалена'
        ], 201);
    }
}
