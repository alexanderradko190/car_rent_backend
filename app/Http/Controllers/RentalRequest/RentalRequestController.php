<?php

namespace App\Http\Controllers\RentalRequest;

use App\DTO\RentalRequest\CreateRentalRequestDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RentalRequest\RentalRequestRequest;
use App\Services\RentalRequest\RentalRequestService;
use Illuminate\Http\JsonResponse;

class RentalRequestController extends Controller
{
    public function __construct(
        private RentalRequestService $service
    ) {
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
        $data = $request->validated();

        $client = auth()->user()?->client;

        if (!$client) {
            return response()->json([
                'message' => 'Вы не являетесь клиентом'
            ], 422);
        }

        $dto = new CreateRentalRequestDTO(
            $client->id,
            $data['car_id'],
            $data['start_time'],
            $data['end_time'],
            $data['insurance_option'],
            $data['agreement_accepted']
        );

        $result = $this->service->create($dto);

        if (isset($result['error'])) {
            return response()->json($result, 400);
        }

        return response()->json([
            'message' => 'Заявка на аренду создана',
            'data' => $result['data']
        ], 201);
    }

    public function approve($id): JsonResponse
    {
        $rentRequest = $this->service->approve($id);

        if (isset($rentRequest['error'])) {
            return response()->json([
                'message' => $rentRequest['error']
            ], 404);
        }

        return response()->json([
            'message' => 'Заявка на аренду подтверждена'
        ], 201);
    }

    public function reject($id): JsonResponse
    {
        $rentRequest = $this->service->reject($id);

        if (isset($rentRequest['error'])) {
            return response()->json([
                'message' => $rentRequest['error']
            ], 404);
        }

        return response()->json([
            'message' => 'Заявка на аренду отклонена'
        ], 201);
    }

    public function complete($id): JsonResponse
    {
        $rentRequest = $this->service->complete($id);

        if (isset($rentRequest['error'])) {
            return response()->json([
                'message' => $rentRequest['error']
            ], 404);
        }

        return response()->json([
            'message' => 'Аренда завершена'
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $rentRequest = $this->service->delete($id);

        if (isset($rentRequest['error'])) {
            return response()->json([
                'message' => $rentRequest['error']
            ], 404);
        }

        return response()->json([
            'message' => 'Заявка на аренду удалена'
        ], 201);
    }
}
