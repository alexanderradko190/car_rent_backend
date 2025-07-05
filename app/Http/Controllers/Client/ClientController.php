<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientCreateRequest;
use App\Http\Requests\Client\ClientLicenseScanRequest;
use App\Http\Requests\Client\ClientUpdateRequest;
use App\Models\Client\Client;
use App\Services\Client\ClientService;
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

    public function export()
    {
        $header = [
            'id' => 'ID',
            'full_name' => 'ФИО',
            'age' => 'Возраст',
            'phone' => 'Телефон',
            'email' => 'Email',
            'driving_experience' => 'Опыт вождения',
            'license_scan' => 'Вод. удостоверение',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения'
        ];

        $callback = function () use ($header) {
            $handle = fopen('php://output', 'w');
            // BOM для Excel
            fwrite($handle, "\xEF\xBB\xBF");
            // Русские заголовки
            fputcsv($handle, array_values($header), ';');

            // Данные клиентов (ленивая загрузка)
            foreach (Client::cursor() as $client) {
                $row = [
                    $client->id,
                    $client->full_name,
                    $client->age,
                    $client->phone,
                    $client->email,
                    $client->driving_experience,
                    $client->license_scan,
                    $client->created_at,
                    $client->updated_at,
                ];
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="clients.csv"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
