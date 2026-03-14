<?php

namespace Database\Factories\RentalRequest;

use App\Enums\Car\RentalStatus;
use App\Models\Car\Car;
use App\Models\Client\Client;
use App\Models\RentalRequest\RentalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalRequestFactory extends Factory
{
    protected $model = RentalRequest::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+2 weeks');
        $end = $this->faker->dateTimeBetween($start->format('Y-m-d H:i:s') . ' +1 day', $start->format('Y-m-d H:i:s') . ' +14 days');

        $hours = max(1, ($end->getTimestamp() - $start->getTimestamp()) / 3600);
        $hourlyRate = $this->faker->randomElement([500, 1000, 1500]);
        $totalCost = round($hours * $hourlyRate * (1 + ($this->faker->boolean(30) ? 0.15 : 0)), 2);

        return [
            'client_id' => Client::factory(),
            'car_id' => Car::factory(),
            'start_time' => $start,
            'end_time' => $end,
            'total_cost' => $totalCost,
            'insurance_option' => $this->faker->boolean(40),
            'status' => $this->faker->randomElement(RentalStatus::cases()),
        ];
    }
}
