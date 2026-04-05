<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LuponCase>
 */
class LuponCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'case_number' => 'BC-' . now()->year . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'complainant' => fake()->name(),
            'complainant_address' => fake()->address(),
            'complainant_phone' => fake()->phoneNumber(),
            'complainant_id' => null,
            'respondent' => fake()->name(),
            'respondent_address' => fake()->address(),
            'respondent_phone' => fake()->phoneNumber(),
            'respondent_id' => null,
            'nature_of_case' => fake()->randomElement([
                'Noise Complaint',
                'Property Dispute',
                'Verbal Altercation',
                'Boundary Dispute',
                'Debt Collection',
                'Harassment',
                'Trespassing',
                'Damage to Property'
            ]),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'ongoing', 'settled', 'unsettled', 'dismissed']),
            'filed_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'filed_by' => null,
            'settled_at' => null,
            'certificate_of_settlement' => null,
        ];
    }
}
