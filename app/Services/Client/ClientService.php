<?php

namespace App\Services\Client;

use App\Models\Client\Client;
use App\Repositories\Client\ClientRepository;
use App\DTO\Client\CreateClientDTO;

class ClientService
{
    public function __construct(private ClientRepository $repository) {}

    public function create(CreateClientDTO $dto)
    {
        return $this->repository->create([
            'full_name' => $dto->full_name,
            'age' => $dto->age,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'driving_experience' => $dto->driving_experience,
            'license_scan' => $dto->license_scan,
        ]);
    }

    public function update(Client $client, array $data)
    {
        return $this->repository->update($client, $data);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function delete(Client $client)
    {
        $this->repository->delete($client);
    }
}
