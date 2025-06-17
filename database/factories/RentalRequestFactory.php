<?php

namespace Database\Factories;

use App\Enums\RentalStatus;
use App\Models\RentalRequest;
use App\Models\User;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalRequestFactory extends Factory
{
    protected $model = RentalRequest::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'car_id' => Car::factory(),
            'start_time' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_time' => $this->faker->dateTimeBetween('+2 days', '+2 weeks'),
            'total_cost' => $this->faker->randomFloat(2, 1000, 10000),
            'insurance_option' => $this->faker->boolean,
            'status' => $this->faker->randomElement(RentalStatus::cases()),
        ];
    }
}
