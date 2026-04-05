<?php

namespace Tests\Feature;

use App\Models\LuponCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlaIndicatorViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_sla_card_present_for_mediation_phase_case(): void
    {
        $case = LuponCase::factory()->create([
            'status'     => 'under_mediation',
            'filed_date' => now()->subDays(10)->toDateString(),
            'settled_at' => null,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cases.show', $case))
            ->assertOk()
            ->assertSee('SLA Deadline')
            ->assertSee('On Track')
            ->assertSee('sla-on-track');
    }

    public function test_overdue_banner_appears_when_overdue(): void
    {
        $case = LuponCase::factory()->create([
            'status'     => 'under_mediation',
            'filed_date' => now()->subDays(35)->toDateString(),
            'settled_at' => null,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cases.show', $case))
            ->assertOk()
            ->assertSeeText('overdue', false)
            ->assertSee('sla-overdue');
    }

    public function test_overdue_banner_absent_when_not_overdue(): void
    {
        $case = LuponCase::factory()->create([
            'status'     => 'under_mediation',
            'filed_date' => now()->subDays(10)->toDateString(),
            'settled_at' => null,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cases.show', $case))
            ->assertOk()
            ->assertDontSee('Immediate action required');
    }

    public function test_resolved_state_renders_for_settled_case(): void
    {
        $case = LuponCase::factory()->create([
            'status'     => 'settled',
            'filed_date' => now()->subDays(20)->toDateString(),
            'settled_at' => now(),
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cases.show', $case))
            ->assertOk()
            ->assertSee('Resolved')
            ->assertSee('sla-resolved');
    }
}
