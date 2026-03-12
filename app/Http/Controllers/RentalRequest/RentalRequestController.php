<?php

namespace App\Http\Controllers\RentalRequest;

use App\DTO\RentalRequest\CreateRentalRequestDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\RentalRequestResource;
use App\Http\Requests\RentalRequest\RentalRequestRequest;
use App\Http\Requests\RentalRequest\SendAgreementRequest;
use App\Services\RentalRequest\AgreementDeliveryService;
use App\Services\RentalRequest\RentalRequestService;
use Illuminate\Http\JsonResponse;

class RentalRequestController extends Controller
{
    public function __construct(
        private RentalRequestService $service,
        private AgreementDeliveryService $agreementDeliveryService
    ) {
        //
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => RentalRequestResource::collection($this->service->all()) ?? null
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
            'data' => RentalRequestResource::make($request) ?? null
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

        return response()->json([
            'message' => 'Заявка на аренду создана',
            'data' => RentalRequestResource::make($result) ?? null
        ], 201);
    }

    public function approve($id): JsonResponse
    {
        $this->service->approve($id);

        return response()->json([
            'message' => 'Заявка на аренду подтверждена'
        ], 201);
    }

    public function reject($id): JsonResponse
    {
        $this->service->reject($id);

        return response()->json([
            'message' => 'Заявка на аренду отклонена'
        ], 201);
    }

    public function complete($id): JsonResponse
    {
        $this->service->complete($id);

        return response()->json([
            'message' => 'Аренда завершена'
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Заявка на аренду удалена'
        ], 201);
    }

    public function sendAgreement(SendAgreementRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        $attempt = $this->agreementDeliveryService->sendForRentalRequestId(
            $id,
            $data['rent_history_id']
        );

        return response()->json([
            'message' => 'Договор отправлен',
            'data' => [
                'delivery_id' => $attempt->id,
                'status' => $attempt->status,
            ],
        ], 202);
    }
}
