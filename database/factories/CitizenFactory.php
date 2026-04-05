<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Citizen>
 */
class CitizenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'purok' => 'Purok ' . fake()->numberBetween(1, 10),
            'address' => 'Brgy. ' . fake()->streetName() . ', ' . fake()->city(),
            'phone' => fake()->numerify('09#########'),
        ];
    }
}
