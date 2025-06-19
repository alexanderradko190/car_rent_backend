<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\ClientLicenseScanRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Services\ClientService;
use App\DTO\Client\CreateClientDTO;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function __construct(private ClientService $service)
    {
    }

    public function index()
    {
        return response()->json([
            'data' => $this->service->all(),
        ]);
    }

    public function store(ClientCreateRequest $request)
    {
        $dto = new CreateClientDTO(...$request->validated());

        if ($request->hasFile('license_scan')) {
            $dto->license_scan = $request->file('license_scan')->store('licenses', 'public');
        }

        $client = $this->service->create($dto);

        return response()->json([
            'message' => 'Клиент добавлен',
            'data' => $client,
        ], 201);
    }

    public function show($id)
    {
        $client = $this->service->find($id);

        if (!$client) {
            return response()->json(['message' => 'Клиент не найден'], 404);
        }

        $data = $client->toArray();

        if ($client->license_scan) {
            $data['license_scan'] = Storage::disk('public')->url($client->license_scan);
        } else {
            $data['license_scan'] = null;
        }

        return response()->json(['data' => $data]);
    }

    public function update(ClientUpdateRequest $request, $id)
    {
        $client = $this->service->find($id);

        if (!$client) {
            return response()->json(['message' => 'Клиент не найден'], 404);
        }

        $client = $this->service->update($client, $request->only(['phone', 'email', 'driving_experience']));

        return response()->json(['data' => $client]);
    }

    public function updateLicenseScan(ClientLicenseScanRequest $request, $id)
    {
        $client = $this->service->find($id);

        if (!$client) {
            return response()->json(['message' => 'Клиент не найден'], 404);
        }

        if ($client->license_scan) {
            Storage::disk('public')->delete($client->license_scan);
        }

        $file = $request->file('license_scan');
        $path = $file->store('licenses', 'public');
        $client->license_scan = $path;
        $client->save();

        return response()->json([
            'message' => 'Водительское удостоверение обновлено',
            'license_scan' => Storage::disk('public')->url($path),
        ]);
    }

    public function destroy($id)
    {
        $client = $this->service->find($id);

        if (!$client) {
            return response()->json(['message' => 'Клиент не найден'], 404);
        }

        $this->service->delete($client);

        return response()->json(['message' => 'Клиент удалён']);
    }
}
