<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaseStatusHistory>
 */
class CaseStatusHistoryFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['filed', 'under_mediation', 'under_conciliation', 'under_arbitration', 'settled', 'certified_to_court', 'dismissed', 'withdrawal'];

        return [
            'lupon_case_id' => null,
            'old_status' => fake()->randomElement($statuses),
            'new_status' => fake()->randomElement($statuses),
            'remarks' => fake()->optional(0.5)->sentence(),
            'changed_by' => null,
        ];
    }
}
