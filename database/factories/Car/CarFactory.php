<?php

namespace Database\Factories\Car;

use App\Enums\Car\CarClass;
use App\Enums\Car\CarStatus;
use App\Models\Car\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    private const MAKES_MODELS = [
        'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Land Cruiser', 'Yaris'],
        'Lada' => ['Vesta', 'Granta', 'XRAY', 'Niva'],
        'Kia' => ['Rio', 'Optima', 'Sportage', 'K5', 'Sorento'],
        'Hyundai' => ['Solaris', 'Elantra', 'Creta', 'Tucson', 'Sonata'],
        'Volkswagen' => ['Polo', 'Tiguan', 'Passat', 'Golf', 'Touareg'],
        'Skoda' => ['Octavia', 'Rapid', 'Kodiaq', 'Superb', 'Karoq'],
        'BMW' => ['320', '520', 'X3', 'X5', '530'],
        'Mercedes-Benz' => ['C 200', 'E 220', 'GLC', 'GLE', 'A 180'],
        'Nissan' => ['Qashqai', 'X-Trail', 'Murano', 'Kicks', 'Terrano'],
        'Mazda' => ['3', '6', 'CX-5', 'CX-30', 'CX-9'],
    ];

    public function definition(): array
    {
        $carClass = $this->faker->randomElement(CarClass::cases());
        $make = $this->faker->randomElement(array_keys(self::MAKES_MODELS));
        $models = self::MAKES_MODELS[$make];
        $model = $this->faker->randomElement($models);

        $year = $this->faker->numberBetween(2018, (int) date('Y'));
        $power = match ($carClass) {
            CarClass::ECONOMY => $this->faker->numberBetween(90, 150),
            CarClass::COMFORT => $this->faker->numberBetween(150, 250),
            CarClass::BUSINESS => $this->faker->numberBetween(200, 400),
        };

        return [
            'make' => $make,
            'model' => $model,
            'year' => $year,
            'vin' => $this->faker->unique()->regexify('[A-Z0-9]{17}'),
            'license_plate' => $this->faker->unique()->regexify('[A-Z]\d{3}[A-Z]{2}\d{2}'),
            'car_class' => $carClass,
            'power' => $power,
            'hourly_rate' => $carClass->hourlyRate(),
            'status' => $this->faker->randomElement(CarStatus::cases()),
            'current_renter_id' => null,
        ];
    }
}
