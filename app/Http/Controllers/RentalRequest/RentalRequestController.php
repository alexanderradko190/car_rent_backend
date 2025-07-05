<?php

namespace App\Http\Controllers\RentalRequest;

use App\DTO\RentalRequest\CreateRentalRequestDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RentalRequest\RentalRequestRequest;
use App\Services\RentalRequest\RentalRequestService;

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
        $validated['client_id'] = auth()->id();

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
