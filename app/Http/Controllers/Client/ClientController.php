<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientCreateRequest;
use App\Http\Requests\Client\ClientLicenseScanRequest;
use App\Http\Requests\Client\ClientUpdateRequest;
use App\Services\Client\ClientService;
use App\DTO\Client\CreateClientDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function __construct(
        private ClientService $service
    ) {
        //
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->service->all(),
        ]);
    }

    public function store(ClientCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();

        $dto = new CreateClientDTO(
            $data['user_id'],
            $data['full_name'],
            $data['age'],
            $data['phone'],
            $data['email'],
            $data['driving_experience'] ?? null,
            $data['license_scan'] ?? null
        );

        if ($request->hasFile('license_scan')) {
            $dto->license_scan = $request->file('license_scan')->store('licenses', 'public');
        }

        $client = $this->service->create($dto);

        return response()->json([
            'message' => 'Клиент добавлен',
            'data' => $client,
        ], 201);
    }


    public function show($id): JsonResponse
    {
        $client = $this->service->find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Клиент не найден'
            ], 404);
        }

        $data = $client->toArray();

        if ($client->license_scan) {
            $data['license_scan'] = Storage::disk('public')->url($client->license_scan);
        } else {
            $data['license_scan'] = null;
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function update(ClientUpdateRequest $request, $id): JsonResponse
    {
        $client = $this->service->find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Клиент не найден'
            ], 404);
        }

        $data = $request->validated();

        $this->service->update($client, $data);

        return response()->json([
            'message' => 'Данные клиента обновлены'
        ]);
    }

    public function updateLicenseScan(ClientLicenseScanRequest $request, $id): JsonResponse
    {
        $client = $this->service->find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Клиент не найден'
            ], 404);
        }

        if ($client->license_scan) {
            Storage::disk('public')->delete($client->license_scan);
        }

        $file = $request->file('license_scan');
        $path = $file->store('licenses', 'public');
        $client->license_scan = $path;

        $client->save();

        return response()->json([
            'message' => 'Водительское удостоверение обновлено'
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $client = $this->service->find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Клиент не найден'
            ], 404);
        }

        if ($client->car) {
            return response()->json([
                'message' => 'Нельхя удалить клиента, который привязан к автомобилю'
            ], 400);
        }

        $this->service->delete($client);

        return response()->json([
            'message' => 'Клиент удалён'
        ]);
    }
}
