<?php

namespace App\Services\Report;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class ReportDataService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.go_export.base_uri'),
            'timeout' => 20,
        ]);
    }

    public function rentHistories(string $from, string $to, int $page, int $perPage): array
    {
        return $this->get('/api/export/rent_histories', $from, $to, $page, $perPage);
    }

    public function rentalRequests(string $from, string $to, int $page, int $perPage): array
    {
        return $this->get('/api/export/rental_requests', $from, $to, $page, $perPage);
    }

    private function get(string $path, string $from, string $to, int $page, int $perPage): array
    {
        try {
            $response = $this->client->get($path, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'from' => $from,
                    'to' => $to,
                    'page' => $page,
                    'per_page' => $perPage,
                ],
            ]);

            $payload = json_decode((string) $response->getBody(), true);

            if (!is_array($payload) || !isset($payload['meta'], $payload['data'])) {
                throw new RuntimeException('Некорректный ответ от Go service');
            }

            return $payload;
        } catch (GuzzleException $e) {
            throw new RuntimeException('Go service error: ' . $e->getMessage(), 0, $e);
        }
    }
}
