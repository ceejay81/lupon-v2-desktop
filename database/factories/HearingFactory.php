<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hearing>
 */
class HearingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lupon_case_id' => null,
            'hearing_type' => fake()->randomElement(['mediation', 'conciliation', 'arbitration']),
            'scheduled_at' => fake()->dateTimeBetween('now', '+2 months'),
            'location' => fake()->randomElement(['Barangay Hall', 'Conference Room A', 'Meeting Room B', 'Community Center']),
            'minutes' => null,
            'outcome' => null,
            'status' => 'scheduled',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'outcome' => fake()->randomElement(['Settled', 'Referred to Conciliation', 'Referred to Arbitration', 'No Agreement']),
            'minutes' => fake()->paragraph(),
        ]);
    }
}
