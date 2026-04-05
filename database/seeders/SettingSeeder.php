<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'barangay_name', 'value' => 'Barangay Bula', 'type' => 'string', 'group' => 'general'],
            ['key' => 'city_municipality', 'value' => 'General Santos City', 'type' => 'string', 'group' => 'general'],
            ['key' => 'province', 'value' => 'South Cotabato', 'type' => 'string', 'group' => 'general'],
            ['key' => 'monthly_report_deadline_day', 'value' => '15', 'type' => 'integer', 'group' => 'reports'],
            ['key' => 'enable_auto_archive', 'value' => 'true', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'archive_after_days', 'value' => '365', 'type' => 'integer', 'group' => 'system'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
