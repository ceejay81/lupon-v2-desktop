<?php

namespace Tests\Feature;

use App\Models\LuponCase;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Feature: case-show-page-improvements, Property 2: Terminal status yields resolved state
class SlaDataTerminalStatusTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Property 2: Terminal status yields resolved state
     * Validates: Requirements 1.5
     *
     * For any LuponCase whose status is a terminal status, getSlaData() must
     * return state 'resolved' with both days_remaining and days_overdue null.
     */
    public function test_terminal_status_yields_resolved_state(): void
    {
        $terminalStatuses = ['settled', 'certified_to_court', 'dismissed', 'withdrawal', 'archived'];

        $this->forAll(
            Generators::elements($terminalStatuses),
            Generators::choose(0, 60)
        )->then(function (string $status, int $daysOffset) {
            $filedDate = now()->subDays($daysOffset)->startOfDay();

            /** @var LuponCase $case */
            $case = LuponCase::factory()->create([
                'status' => $status,
                'filed_date' => $filedDate,
                'settled_at' => null,
            ]);

            $slaData = $case->sla_data;

            $this->assertSame('resolved', $slaData['state'], "Expected resolved for terminal status '{$status}'");
            $this->assertSame('Resolved', $slaData['label'], "Expected 'Resolved' label for terminal status '{$status}'");
            $this->assertNull($slaData['days_remaining'], "days_remaining must be null for terminal status '{$status}'");
            $this->assertNull($slaData['days_overdue'], "days_overdue must be null for terminal status '{$status}'");
        });
    }
}
