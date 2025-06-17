<?php

namespace Database\Factories;

use App\Enums\CarClass;
use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition()
    {
        $carClass = $this->faker->randomElement(CarClass::cases());
        return [
            'make' => $this->faker->randomElement(['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes']),
            'model' => $this->faker->word,
            'year' => $this->faker->numberBetween(2000, date('Y')),
            'vin' => $this->faker->unique()->regexify('[A-HJ-NPR-Z0-9]{17}'),
            'license_plate' => $this->faker->unique()->regexify('[A-Z]{1}\d{3}[A-Z]{2}'),
            'car_class' => $carClass,
            'power' => $this->faker->numberBetween(100, 500),
            'hourly_rate' => $carClass->hourlyRate(),
            'status' => $this->faker->randomElement(CarStatus::cases()),
            'current_renter_id' => null,
        ];
    }
}
