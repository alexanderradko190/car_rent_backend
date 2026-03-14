<?php

namespace Database\Seeders;

use App\Models\Car\Car;
use App\Models\Client\Client;
use App\Models\RentalRequest\RentalRequest;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    public function run(): void
    {
        $cars = Car::factory()->count(20)->create();
        $clients = Client::factory()->count(30)->create();

        foreach (range(1, 50) as $i) {
            RentalRequest::factory()->create([
                'car_id' => $cars->random()->id,
                'client_id' => $clients->random()->id,
            ]);
        }
    }
}
