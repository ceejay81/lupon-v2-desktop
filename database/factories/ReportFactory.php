<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    public function definition(): array
    {
        $month = fake()->numberBetween(1, 12);
        $year = fake()->numberBetween(now()->year - 2, now()->year);

        return [
            'type' => fake()->randomElement([
                'Monthly Transmittal Report',
                'Endorsement Letter',
                'General Summary',
                'DILG Submission',
                'Quarterly Report',
                'Annual Compliance Report',
            ]),
            'filename' => null,
            'month' => $month,
            'year' => $year,
            'file_path' => null,
            'remarks' => fake()->optional(0.4)->sentence(),
            'status' => 'finalized',
            'content' => null,
            'submitted_at' => now(),
            'submitted_by' => null,
        ];
    }

    public function withFile(): static
    {
        return $this->state(function (array $attributes) {
            $filename = 'Report_'.$attributes['year'].'_'.str_pad($attributes['month'], 2, '0', STR_PAD_LEFT).'.pdf';

            return [
                'filename' => $filename,
                'file_path' => 'reports/'.$attributes['year'].'-'.str_pad($attributes['month'], 2, '0', STR_PAD_LEFT).'/'.time().'_'.$filename,
            ];
        });
    }
}
