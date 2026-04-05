<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hearing>
 */
class HearingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hearing_type' => fake()->randomElement(['mediation', 'conciliation', 'arbitration']),
            'scheduled_at' => fake()->dateTimeBetween('now', '+2 months'),
            'location' => fake()->randomElement(['Barangay Hall', 'Conference Room A', 'Meeting Room B', 'Community Center']),
            'minutes' => null,
            'outcome' => null,
            'status' => 'scheduled',
        ];
    }
}
