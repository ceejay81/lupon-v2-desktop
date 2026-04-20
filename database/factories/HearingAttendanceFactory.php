<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HearingAttendance>
 */
class HearingAttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hearing_id' => null,
            'party_type' => fake()->randomElement(['complainant', 'respondent', 'lupon_member']),
            'name' => fake()->name(),
            'lupon_member_id' => null,
            'attended' => fake()->boolean(75),
            'signature_path' => null,
        ];
    }
}
