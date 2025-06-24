<?php

namespace App\Services;

use GuzzleHttp\Client;

class GoExportService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'http://localhost:8002']);
    }

    public function export(string $type): array
    {
        $response = $this->client->get("/export/{$type}");
        return json_decode((string)$response->getBody(), true);
    }
}
