<?php

namespace App\Repositories\Client;

use App\Models\Client\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository
{
    public function create(array $data): Client
    {
        return Client::create($data);
    }
    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client;
    }
    public function find(int $id): ?Client
    {
        return Client::find($id);
    }
    public function all(): Collection
    {
        return Client::all();
    }
    public function delete(Client $client): void
    {
        $client->delete();
    }
}
