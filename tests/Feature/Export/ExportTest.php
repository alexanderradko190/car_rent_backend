<?php

namespace Tests\Feature\Export;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\DataProvider('exportRoutesProvider')]
    public function test_export_endpoints($route, $expectedContentType)
    {
        $resp = $this->get($route);
        $resp->assertStatus(200)
            ->assertHeader('content-type', $expectedContentType);
    }

    public static function exportRoutesProvider(): array
    {
        return [
            ['/api/clients/export', 'text/csv; charset=UTF-8'],
        ];
    }

    public function test_go_export_integration()
    {
        $response = $this->get('/api/export/cars');
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertIsArray($json);
    }
}
