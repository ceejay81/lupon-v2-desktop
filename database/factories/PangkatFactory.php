<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pangkat>
 */
class PangkatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lupon_case_id' => null,
            'chairperson_id' => null,
            'secretary_id' => null,
            'member_id' => null,
            'assigned_at' => now(),
        ];
    }
}
