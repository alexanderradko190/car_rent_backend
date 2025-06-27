<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'full_name' => $this->faker->name,
            'age' => $this->faker->numberBetween(18, 65),
            'phone' => $this->faker->unique()->numerify('9#########'),
            'email' => $this->faker->unique()->safeEmail,
            'driving_experience' => $this->faker->numberBetween(0, 47),
            'license_scan' => null,
        ];
    }
}
