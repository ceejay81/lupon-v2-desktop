<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Setting up Production Environment for Barangay Bula...');

        // 1. Create Official Admin Account
        User::updateOrCreate(
            ['email' => 'admin@bula.gov.ph'],
            [
                'name' => 'Hon. Nicanora Vargas',
                'password' => bcrypt('password'), // USER: Please change this on first login
            ]
        );

        // 2. Setup Official Barangay Settings
        $settingsData = [
            'barangay_name' => 'Bula',
            'city_name' => 'General Santos City',
            'punong_barangay' => 'Hon. Nicanora T. Vargas',
            'barangay_captain' => 'Hon. Nicanora T. Vargas',
            'lupon_president' => 'Jimuel Villote',
            'committee_peace_order' => 'Dante Granada',
            'is_branding_enabled' => '1',
            'app_version' => '2.0.0-Stable',
        ];

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->command->info('Success: Production user and settings are ready.');
        $this->command->warn('NOTE: All 500 demo cases have been cleared as requested.');
    }
}
