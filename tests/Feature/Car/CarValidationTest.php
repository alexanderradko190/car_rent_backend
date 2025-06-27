<?php

namespace Tests\Feature\Car;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Car;

class CarValidationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidCarDataProvider')]
    public function test_invalid_car_data($payload, $expectedErrors)
    {
        if (isset($payload['vin']) && $payload['vin'] === 'JTHBK1GG7G2212345') {
            Car::factory()->create(['vin' => 'JTHBK1GG7G2212345', 'license_plate' => 'A123BC99']);
        }

        $resp = $this->postJson('/api/cars', $payload);

        if (count($expectedErrors) > 0) {
            $resp->assertStatus(422);
            foreach ($expectedErrors as $field) {
                $resp->assertJsonValidationErrors($field);
            }
        } else {
            $resp->assertStatus(201);
        }
    }

    public static function invalidCarDataProvider(): array
    {
        return [
            'missing required fields' => [
                [
                    'make' => '',
                    'model' => '',
                    'year' => '',
                    'vin' => '',
                    'license_plate' => '',
                    'car_class' => '',
                    'power' => '',
                    'hourly_rate' => '',
                ],
                ['make', 'model', 'year', 'vin', 'license_plate', 'car_class', 'power', 'hourly_rate'],
            ],
            'invalid year and vin' => [
                [
                    'make' => 'Toyota',
                    'model' => 'Camry',
                    'year' => 1800,
                    'vin' => 'INVALIDVIN',
                    'license_plate' => 'B123BC99',
                    'car_class' => 'economy',
                    'power' => 100,
                    'hourly_rate' => 100,
                ],
                ['year', 'vin'],
            ],
            'non-unique vin' => [
                [
                    'make' => 'Toyota',
                    'model' => 'Camry',
                    'year' => 2020,
                    'vin' => 'JTHBK1GG7G2212345',
                    'license_plate' => 'B124BC99',
                    'car_class' => 'business',
                    'power' => 200,
                    'hourly_rate' => 1500,
                ],
                ['vin'],
            ],
        ];
    }
}
