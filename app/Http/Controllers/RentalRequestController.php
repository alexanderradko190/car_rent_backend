<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequestRequest;
use App\Services\RentalRequestService;
use App\DTO\RentalRequest\CreateRentalRequestDTO;

class RentalRequestController extends Controller
{
    public function __construct(private RentalRequestService $service)
    {
    }

    public function index()
    {
        return response()->json(['data' => $this->service->all()]);
    }

    public function show($id)
    {
        $request = $this->service->find($id);
        if (!$request) {
            return response()->json(['message' => 'Заявка не найдена'], 404);
        }
        return response()->json(['data' => $request]);
    }

    public function store(RentalRequestRequest $request)
    {
        $validated = $request->validated();

        // ! Хардкод id клиента для теста (замени 1 на свой существующий id)
        // $validated['client_id'] = auth()->id();
        $validated['client_id'] = 2; // TODO: вернуть auth()->id() когда будет авторизация

//        $dto = new CreateRentalRequestDTO(...$request->validated()); TODO: вернуть, когда будет авторизация

        $dto = new CreateRentalRequestDTO(...$validated);

        $result = $this->service->create($dto);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }
        return response()->json(['message' => 'Заявка создана', 'data' => $result['data']], 201);
    }

    public function approve($id)
    {
        $result = $this->service->approve($id);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }
        return response()->json(['message' => $result['message'], 'data' => $result['data']]);
    }

    public function reject($id)
    {
        $result = $this->service->reject($id);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }
        return response()->json(['message' => $result['message']]);
    }

    public function complete($id)
    {
        $result = $this->service->complete($id);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }
        return response()->json(['message' => $result['message'], 'data' => $result['data'] ?? null]);
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }
        return response()->json(['message' => $result['message']]);
    }
}
