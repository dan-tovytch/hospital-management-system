<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'street' => $this->faker->streetName(),
            'number' => $this->faker->buildingNumber(),
            'cep' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'neighborhood' => $this->faker->streetName(),
            'state' => $this->faker->state(),
        ];
    }
}
