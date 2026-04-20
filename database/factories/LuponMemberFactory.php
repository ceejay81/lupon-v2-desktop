<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LuponMember>
 */
class LuponMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'position' => 'Lupon Member',
            'member_type' => 'regular',
            'appointment_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'is_active' => fake()->boolean(90),
        ];
    }
}
