<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\Document;
use App\Models\Hearing;
use App\Models\LuponCase;
use App\Models\LuponMember;
use App\Models\Pangkat;
use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\View;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Final High-Fidelity Seeding...');

        // 1. Setup Admin
        $admin = User::first() ?? User::factory()->create([
            'name' => 'Hon. Nicanora Vargas',
            'email' => 'admin@bula.gov.ph',
            'password' => bcrypt('password'),
        ]);

        // 2. Setup Settings
        $settingsData = [
            'barangay_name' => 'Bula',
            'city_name' => 'General Santos City',
            'punong_barangay' => 'Hon. Nicanora T. Vargas',
            'barangay_captain' => 'Hon. Nicanora T. Vargas',
            'is_branding_enabled' => '1',
            'lupon_president' => 'Jimuel Villote',
        ];

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        $settings = Setting::pluck('value', 'key')->toArray();

        // 3. Create Citizens
        $citizens = Citizen::factory(100)->create([
            'address' => fn () => 'Brgy. Bula, General Santos City',
            'purok' => fn () => 'Purok '.rand(1, 15),
        ]);

        // 4. Create Lupon Member Pool
        $luponNames = [
            'Roberto Santos', 'Elena Cruz', 'Mario Dimataga', 'Liza Soberano', 'Ricardo Dalisay',
            'Nena Fernandez', 'Gerry Peñalosa', 'Teresita Recto', 'Benny Garciano', 'Lando Calero',
        ];
        $members = collect();
        foreach ($luponNames as $name) {
            $members->push(LuponMember::create(['name' => $name, 'position' => 'Lupon Member', 'is_active' => true]));
        }

        // 5. Generate 500 Cases
        $this->command->info('Creating 500 cases with full audit trails...');
        $totalCases = 500;
        $activePeriods = [];

        for ($i = 0; $i < $totalCases; $i++) {
            $month = rand(1, 12);
            $filedDate = Carbon::create(2026, $month, rand(1, 14));

            $isSettled = rand(1, 10) > 2;
            $status = $isSettled ? 'settled' : 'certified_to_court';

            $case = LuponCase::create([
                'case_number' => 'BP-2026-'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'complainant' => ($c = $citizens->random())->name, 'complainant_id' => $c->id,
                'respondent' => ($r = $citizens->random())->name, 'respondent_id' => $r->id,
                'nature_of_case' => 'Civil Dispute', 'status' => $status,
                'filed_date' => $filedDate, 'filed_by' => $admin->id, 'created_at' => $filedDate,
            ]);

            $case->complainants()->attach($c->id, ['role' => 'complainant']);
            $case->respondents()->attach($r->id, ['role' => 'respondent']);

            // Pangkat & Documents & Hearings
            Pangkat::create([
                'lupon_case_id' => $case->id, 'chairperson_id' => $members->random()->id,
                'secretary_id' => $members->random()->id, 'member_id' => $members->random()->id,
                'assigned_at' => $filedDate->copy()->addDays(2),
            ]);

            Document::create([
                'lupon_case_id' => $case->id, 'document_type' => 'Summons (KP Form 9)',
                'filename' => 'KP9.pdf', 'file_path' => 'digital', 'uploaded_by' => $admin->id,
            ]);

            $hDate = $filedDate->copy()->addDays(7);
            Hearing::create([
                'lupon_case_id' => $case->id, 'hearing_type' => 'mediation', 'location' => 'Hall',
                'scheduled_at' => $hDate, 'status' => 'completed',
            ]);

            if ($isSettled) {
                $settledAt = $hDate->copy()->addDays(5);
                $case->update(['settled_at' => $settledAt]);
                Document::create([
                    'lupon_case_id' => $case->id, 'document_type' => 'Amicable Settlement (KP Form 16)',
                    'filename' => 'KP16.pdf', 'file_path' => 'digital', 'uploaded_by' => $admin->id,
                ]);
            } else {
                Document::create([
                    'lupon_case_id' => $case->id, 'document_type' => 'Certification to File Action (KP Form 20)',
                    'filename' => 'KP20.pdf', 'file_path' => 'digital', 'uploaded_by' => $admin->id,
                ]);
            }

            $activePeriods[$filedDate->format('Y-m')] = ['month' => $filedDate->month, 'year' => $filedDate->year];
        }

        // 6. Generate Reports (Authentic Rendering)
        $this->command->info('Seeding Filing Cabinet with full-fidelity fragments...');

        $renderFragment = function ($view, $data) {
            $html = View::make($view, $data)->render();
            // This regex extracts the part between @section('content') and @endsection
            if (preg_match('/<!-- Date Section -->.*?<div class="cc-block">.*?<\/div>/s', $html, $matches)) {
                return $matches[0];
            }
            if (preg_match('/<div class="report-title">.*?<\/div>.*?<div style="clear: both; padding-bottom: 80px;"><\/div>/s', $html, $matches)) {
                return $matches[0];
            }

            return $html;
        };

        foreach ($activePeriods as $period) {
            $m = $period['month'];
            $y = $period['year'];
            $periodDate = Carbon::create($y, $m, 1);
            $monthlyCases = LuponCase::whereMonth('filed_date', $m)->whereYear('filed_date', $y)->get();

            Report::create([
                'month' => $m, 'year' => $y, 'type' => 'monthly-transmittal', 'status' => 'finalized',
                'content' => $renderFragment('documents.monthly-transmittal-report', [
                    'cases' => $monthlyCases, 'settings' => $settings, 'period' => $periodDate, 'month' => $m, 'year' => $y,
                ]),
                'submitted_at' => now(), 'submitted_by' => $admin->id, 'file_path' => 'archive',
            ]);

            Report::create([
                'month' => $m, 'year' => $y, 'type' => 'endorsement', 'status' => 'finalized',
                'content' => $renderFragment('documents.endorsement', [
                    'settings' => $settings, 'period' => $periodDate, 'month' => $m, 'year' => $y,
                ]),
                'submitted_at' => now(), 'submitted_by' => $admin->id, 'file_path' => 'archive',
            ]);
        }

        $this->command->info('Success: Archive populated with full-fidelity reports!');
    }
}
