<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssessmentItem;

class AssessmentItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['code' => 'A1', 'name' => 'Searchable Database of Cases', 'section' => 'A', 'mov_number' => 1],
            ['code' => 'A2', 'name' => 'Folderized Reports per Case', 'section' => 'A', 'mov_number' => 2],
            ['code' => 'A3', 'name' => 'Monthly Report (Form 1)', 'section' => 'A', 'mov_number' => 3],
            ['code' => 'B1', 'name' => 'Settlement Rate > 70%', 'section' => 'B', 'mov_number' => 1],
            ['code' => 'B2', 'name' => 'Compliance with KP Deadlines', 'section' => 'B', 'mov_number' => 2],
            ['code' => 'C1', 'name' => 'Minutes of Lupon Meetings', 'section' => 'C', 'mov_number' => 1],
        ];

        foreach ($items as $item) {
            AssessmentItem::create([
                'item_code' => $item['code'],
                'item_name' => $item['name'],
                'section' => $item['section'],
                'mov_number' => $item['mov_number'],
                'status' => 'pending',
                'completion_percentage' => 0,
            ]);
        }
    }
}
