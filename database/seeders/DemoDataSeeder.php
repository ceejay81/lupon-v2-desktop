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

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting comprehensive demo data seeding...');

        // 1. Setup Admin User
        $admin = User::first() ?? User::factory()->create([
            'name' => 'Hon. Nicanora Vargas',
            'email' => 'admin@bula.gov.ph',
            'password' => bcrypt('password'),
        ]);

        // 2. Setup Settings
        $settingsData = [
            'barangay_name' => 'Bula',
            'city_municipality' => 'General Santos City',
            'province' => 'South Cotabato',
            'punong_barangay' => 'Hon. Nicanora T. Vargas',
            'barangay_captain' => 'Hon. Nicanora T. Vargas',
            'is_branding_enabled' => '1',
            'lupon_president' => 'Jimuel Villote',
        ];

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'string', 'group' => 'general']);
        }

        // 3. Create Citizens Pool
        $this->command->info('Creating 150 citizens...');
        $citizens = Citizen::factory(150)->create([
            'address' => fn () => 'Brgy. Bula, General Santos City',
            'purok' => fn () => 'Purok '.rand(1, 15),
        ]);

        // 4. Create Lupon Members
        $this->command->info('Creating Lupon members...');
        $luponNames = [
            'Roberto Santos', 'Elena Cruz', 'Mario Dimataga', 'Liza Soberano', 'Ricardo Dalisay',
            'Nena Fernandez', 'Gerry Peñalosa', 'Teresita Recto', 'Benny Garciano', 'Lando Calero',
            'Carmen Reyes', 'Pedro Gonzales', 'Sofia Mendoza', 'Antonio Bautista', 'Gloria Ramos',
        ];
        $members = collect();
        foreach ($luponNames as $name) {
            $members->push(LuponMember::create([
                'name' => $name,
                'position' => 'Lupon Member',
                'member_type' => 'regular',
                'appointment_date' => now()->subMonths(rand(6, 24)),
                'is_active' => true,
            ]));
        }

        // 5. Generate Cases with Full Lifecycle
        $this->command->info('Creating 300 cases with documents, hearings, and pangkats...');
        $totalCases = 300;
        $reportPeriods = [];

        for ($i = 0; $i < $totalCases; $i++) {
            // Random filing date within last 18 months
            $month = rand(1, 12);
            $year = rand(2025, 2026);
            $filedDate = Carbon::create($year, $month, rand(1, 28));

            // Determine case outcome
            $isSettled = rand(1, 10) > 3; // 70% settled
            $status = $isSettled ? 'settled' : (rand(1, 10) > 7 ? 'certified_to_court' : 'under_mediation');

            // Pick random citizens
            $complainantCitizen = $citizens->random();
            $respondentCitizen = $citizens->where('id', '!=', $complainantCitizen->id)->random();

            // Create case
            $case = LuponCase::create([
                'case_number' => 'BP-'.$year.'-'.str_pad(LuponCase::max('id') + 1, 4, '0', STR_PAD_LEFT),
                'complainant' => $complainantCitizen->name,
                'complainant_address' => $complainantCitizen->address,
                'complainant_phone' => $complainantCitizen->phone,
                'complainant_id' => $complainantCitizen->id,
                'respondent' => $respondentCitizen->name,
                'respondent_address' => $respondentCitizen->address,
                'respondent_phone' => $respondentCitizen->phone,
                'respondent_id' => $respondentCitizen->id,
                'nature_of_case' => fake()->randomElement([
                    'Noise Complaint', 'Property Dispute', 'Verbal Altercation', 'Boundary Dispute',
                    'Debt Collection', 'Harassment', 'Trespassing', 'Damage to Property',
                    'Family Dispute', 'Water Rights Dispute',
                ]),
                'description' => fake()->paragraph(),
                'status' => $status,
                'filed_date' => $filedDate,
                'filed_by' => $admin->id,
                'date_of_service_summon' => $filedDate->copy()->addDays(rand(2, 5)),
                'remarks' => fake()->optional(0.3)->sentence(),
                'created_at' => $filedDate,
                'updated_at' => $filedDate,
            ]);

            // Attach citizens via pivot
            $case->complainants()->attach($complainantCitizen->id, ['role' => 'complainant']);
            $case->respondents()->attach($respondentCitizen->id, ['role' => 'respondent']);

            // Create Pangkat
            Pangkat::create([
                'lupon_case_id' => $case->id,
                'chairperson_id' => $members->random()->id,
                'secretary_id' => $members->random()->id,
                'member_id' => $members->random()->id,
                'assigned_at' => $filedDate->copy()->addDays(rand(1, 3)),
            ]);

            // Create Initial Documents
            Document::create([
                'lupon_case_id' => $case->id,
                'document_type' => 'Summons',
                'filename' => 'Summons-KP9.pdf',
                'file_path' => "cases/{$case->id}/uploads/summons.pdf",
                'file_size' => rand(50000, 200000),
                'mime_type' => 'application/pdf',
                'uploaded_by' => $admin->id,
                'created_at' => $filedDate->copy()->addDays(1),
            ]);

            Document::create([
                'lupon_case_id' => $case->id,
                'document_type' => 'Complaint',
                'filename' => 'Complaint-Form.pdf',
                'file_path' => "cases/{$case->id}/uploads/complaint.pdf",
                'file_size' => rand(50000, 200000),
                'mime_type' => 'application/pdf',
                'uploaded_by' => $admin->id,
                'created_at' => $filedDate,
            ]);

            // Create Hearing
            $hearingDate = $filedDate->copy()->addDays(rand(7, 14));
            $hearing = Hearing::create([
                'lupon_case_id' => $case->id,
                'hearing_type' => 'mediation',
                'scheduled_at' => $hearingDate,
                'location' => 'Barangay Hall',
                'status' => 'completed',
                'outcome' => $isSettled ? 'Settled' : 'No Agreement',
                'minutes' => fake()->paragraph(),
                'created_at' => $hearingDate->copy()->subDays(5),
            ]);

            // Outcome Documents
            if ($isSettled) {
                $settledAt = $hearingDate->copy()->addDays(rand(1, 7));
                $case->update(['settled_at' => $settledAt]);

                Document::create([
                    'lupon_case_id' => $case->id,
                    'document_type' => 'Amicable Settlement',
                    'filename' => 'Settlement-KP16.pdf',
                    'file_path' => "cases/{$case->id}/uploads/settlement.pdf",
                    'file_size' => rand(100000, 300000),
                    'mime_type' => 'application/pdf',
                    'uploaded_by' => $admin->id,
                    'created_at' => $settledAt,
                ]);
            } elseif ($status === 'certified_to_court') {
                $certifiedAt = $hearingDate->copy()->addDays(rand(15, 30));
                $case->update(['settled_at' => $certifiedAt]);

                Document::create([
                    'lupon_case_id' => $case->id,
                    'document_type' => 'Certification to File Action',
                    'filename' => 'CFA-KP20.pdf',
                    'file_path' => "cases/{$case->id}/uploads/cfa.pdf",
                    'file_size' => rand(80000, 250000),
                    'mime_type' => 'application/pdf',
                    'uploaded_by' => $admin->id,
                    'created_at' => $certifiedAt,
                ]);
            }

            // Track periods for reports
            $periodKey = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT);
            $reportPeriods[$periodKey] = ['month' => $month, 'year' => $year];
        }

        // 6. Generate Monthly Reports
        $this->command->info('Creating monthly reports for active periods...');
        $reportTypes = [
            'Monthly Transmittal Report',
            'Endorsement Letter - Settled Cases',
            'Endorsement Letter - CFA Cases',
            'DILG Compliance Report',
            'Quarterly Summary',
        ];

        foreach ($reportPeriods as $period) {
            $m = $period['month'];
            $y = $period['year'];

            // Create 2-3 reports per month using distinct types
            $shuffledTypes = collect($reportTypes)->shuffle()->take(rand(2, 3));
            foreach ($shuffledTypes as $reportType) {
                $uniqueType = $reportType.' '.Carbon::create($y, $m)->format('F Y');
                $filename = str_replace([' ', '-'], '_', $reportType).'_'.$y.'_'.str_pad($m, 2, '0', STR_PAD_LEFT).'.pdf';

                Report::firstOrCreate(
                    ['type' => $uniqueType, 'month' => $m, 'year' => $y],
                    [
                        'filename' => $filename,
                        'file_path' => "reports/{$y}-".str_pad($m, 2, '0', STR_PAD_LEFT).'/'.time().'_'.$filename,
                        'remarks' => fake()->optional(0.4)->sentence(),
                        'status' => 'finalized',
                        'content' => null,
                        'submitted_at' => Carbon::create($y, $m, rand(15, 28)),
                        'submitted_by' => $admin->id,
                    ]
                );
            }
        }

        $this->command->info('✓ Demo data seeding complete!');
        $this->command->info("  - {$totalCases} cases created");
        $this->command->info('  - '.($totalCases * 2).'+ documents attached');
        $this->command->info('  - '.count($reportPeriods).' active periods');
        $this->command->info('  - '.(count($reportPeriods) * 2).'+ reports generated');
    }
}
