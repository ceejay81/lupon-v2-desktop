<?php

namespace Tests\Feature;

use App\Models\LuponCase;
use Eris\Attributes\ErisRepeat;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Feature: case-show-page-improvements, Property 1: SLA state classification
class SlaDataClassificationTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Property 1: SLA state classification
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4
     *
     * For any LuponCase with status filed or under_mediation, getSlaData()
     * must return a state exactly determined by days_active:
     *   - days_active <= 25  → on_track,    days_remaining == 30 - days_active
     *   - 25 < days_active <= 30 → approaching, days_remaining == 30 - days_active
     *   - days_active > 30   → overdue,     days_overdue == days_active - 30
     */
    #[ErisRepeat(100)]
    public function testSlaStateClassificationMatchesThresholds(): void
    {
        $this->forAll(
            Generators::elements(['filed', 'under_mediation']),
            Generators::choose(0, 60)
        )->then(function (string $status, int $daysOffset) {
            $filedDate = now()->subDays($daysOffset)->startOfDay();

            /** @var LuponCase $case */
            $case = LuponCase::factory()->create([
                'status'     => $status,
                'filed_date' => $filedDate,
                'settled_at' => null,
            ]);

            $daysActive = $case->no_of_days_in_barangay;
            $slaData    = $case->sla_data;

            if ($daysActive <= 25) {
                $this->assertSame('on_track', $slaData['state'], "Expected on_track for {$daysActive} days active");
                $this->assertSame(30 - $daysActive, $slaData['days_remaining'], "days_remaining mismatch for {$daysActive} days active");
                $this->assertNull($slaData['days_overdue'], "days_overdue should be null for on_track");
            } elseif ($daysActive <= 30) {
                $this->assertSame('approaching', $slaData['state'], "Expected approaching for {$daysActive} days active");
                $this->assertSame(30 - $daysActive, $slaData['days_remaining'], "days_remaining mismatch for {$daysActive} days active");
                $this->assertNull($slaData['days_overdue'], "days_overdue should be null for approaching");
            } else {
                $this->assertSame('overdue', $slaData['state'], "Expected overdue for {$daysActive} days active");
                $this->assertNull($slaData['days_remaining'], "days_remaining should be null for overdue");
                $this->assertSame($daysActive - 30, $slaData['days_overdue'], "days_overdue mismatch for {$daysActive} days active");
            }
        });
    }
}
