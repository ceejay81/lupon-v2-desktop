<?php

namespace Tests\Feature;

use App\Models\LuponCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlaIndicatorViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_banner_is_never_rendered(): void
    {
        $case = LuponCase::factory()->create([
            'status' => 'under_mediation',
            'filed_date' => now()->subDays(35)->toDateString(),
            'settled_at' => null,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cases.show', $case))
            ->assertOk()
            ->assertDontSee('Immediate action required')
            ->assertDontSee('class="sla-overdue-banner');
    }
}
