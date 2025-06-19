<?php

namespace App\Services;

use App\Repositories\ClientRepository;
use App\DTO\Client\CreateClientDTO;
use App\Models\Client;

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
