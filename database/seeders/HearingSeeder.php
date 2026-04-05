<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HearingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, let's create some sample cases if they don't exist
        $cases = \App\Models\LuponCase::factory(5)->create();
        
        // Create hearings for the next few weeks
        foreach ($cases as $case) {
            \App\Models\Hearing::factory()->create([
                'lupon_case_id' => $case->id,
                'scheduled_at' => now()->addDays(rand(1, 30)),
                'hearing_type' => collect(['mediation', 'conciliation', 'arbitration'])->random(),
                'status' => collect(['scheduled', 'completed', 'cancelled'])->random(),
                'location' => collect(['Barangay Hall', 'Conference Room A', 'Meeting Room B'])->random(),
            ]);
        }
        
        // Create some past hearings
        foreach ($cases->take(3) as $case) {
            \App\Models\Hearing::factory()->create([
                'lupon_case_id' => $case->id,
                'scheduled_at' => now()->subDays(rand(1, 15)),
                'hearing_type' => collect(['mediation', 'conciliation', 'arbitration'])->random(),
                'status' => collect(['completed', 'failed'])->random(),
                'location' => collect(['Barangay Hall', 'Conference Room A', 'Meeting Room B'])->random(),
            ]);
        }
    }
}
