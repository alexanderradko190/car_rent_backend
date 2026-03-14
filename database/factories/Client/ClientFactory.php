<?php

namespace Database\Factories\Client;

use App\Models\Client\Client;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        $phone = '7' . $this->faker->unique()->numerify('9##########');

        return [
            'user_id' => $user->id,
            'full_name' => $user->name,
            'age' => $this->faker->numberBetween(21, 99),
            'phone' => $phone,
            'email' => $user->email,
            'driving_experience' => $this->faker->numberBetween(1, 99),
            'license_scan' => null,
        ];
    }
}
