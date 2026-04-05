<?php

namespace Tests\Feature;

use App\Models\LuponCase;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Feature: case-show-page-improvements, Property 3: Post-mediation status yields post_mediation state
class SlaDataPostMediationTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Property 3: Post-mediation status yields post_mediation state
     * Validates: Requirements 1.6
     *
     * For any LuponCase whose status is under_conciliation or under_arbitration,
     * getSlaData() must return state 'post_mediation' regardless of days_active,
     * and must not apply the 30-day mediation threshold.
     */
    public function testPostMediationStatusYieldsPostMediationState(): void
    {
        $postMediationStatuses = ['under_conciliation', 'under_arbitration'];

        $this->forAll(
            Generators::elements($postMediationStatuses),
            Generators::choose(0, 60)
        )->then(function (string $status, int $daysOffset) {
            $filedDate = now()->subDays($daysOffset)->startOfDay();

            /** @var LuponCase $case */
            $case = LuponCase::factory()->create([
                'status'     => $status,
                'filed_date' => $filedDate,
                'settled_at' => null,
            ]);

            $slaData = $case->sla_data;

            $this->assertSame('post_mediation', $slaData['state'], "Expected post_mediation for status '{$status}' with {$daysOffset} days offset");
            $this->assertNotSame('on_track', $slaData['state'], "30-day threshold must not be applied for post-mediation status");
            $this->assertNotSame('approaching', $slaData['state'], "30-day threshold must not be applied for post-mediation status");
            $this->assertNotSame('overdue', $slaData['state'], "30-day threshold must not be applied for post-mediation status");
        });
    }
}
