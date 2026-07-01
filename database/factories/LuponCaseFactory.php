<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LuponCase>
 */
class LuponCaseFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['filed', 'under_mediation', 'under_conciliation', 'under_arbitration', 'settled', 'certified_to_court', 'dismissed', 'withdrawal'];
        $status = fake()->randomElement($statuses);
        $filedDate = fake()->dateTimeBetween('-18 months', 'now');

        return [
            'case_number' => 'BP-'.now()->year.'-'.str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'complainant' => fake()->name(),
            'complainant_address' => 'Brgy. Bula, General Santos City',
            'complainant_phone' => fake()->numerify('09#########'),
            'complainant_id' => null,
            'respondent' => fake()->name(),
            'respondent_address' => 'Brgy. Bula, General Santos City',
            'respondent_phone' => fake()->numerify('09#########'),
            'respondent_id' => null,
            'nature_of_case' => fake()->randomElement([
                'Noise Complaint',
                'Property Dispute',
                'Verbal Altercation',
                'Boundary Dispute',
                'Debt Collection',
                'Harassment',
                'Trespassing',
                'Damage to Property',
                'Family Dispute',
                'Water Rights Dispute',
            ]),
            'description' => fake()->paragraph(),
            'status' => $status,
            'filed_date' => $filedDate,
            'filed_by' => null,
            'settled_at' => in_array($status, ['settled', 'certified_to_court', 'dismissed', 'withdrawal'])
                ? fake()->dateTimeBetween($filedDate, 'now')
                : null,
            'date_of_service_summon' => fake()->optional(0.7)->dateTimeBetween($filedDate, 'now'),
            'remarks' => fake()->optional(0.3)->sentence(),
            'completed_steps' => null,
        ];
    }

    public function settled(): static
    {
        return $this->state(fn () => ['status' => 'settled', 'settled_at' => now()->subDays(rand(5, 60))]);
    }

    public function certifiedToCourt(): static
    {
        return $this->state(fn () => ['status' => 'certified_to_court', 'settled_at' => now()->subDays(rand(5, 60))]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => fake()->randomElement(['filed', 'under_mediation', 'under_conciliation']),
            'settled_at' => null,
        ]);
    }
}
