<?php

namespace Tests\Feature;

use App\Models\Citizen;
use App\Models\LuponCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatedCasesPanelViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_related_cases_message_when_empty(): void
    {
        $user = User::factory()->create();
        $citizen = Citizen::factory()->create();

        $case = LuponCase::factory()->create([
            'status' => 'filed',
        ]);
        $case->complainants()->attach($citizen->id, ['role' => 'complainant']);

        $response = $this->actingAs($user)->get(route('cases.show', $case));

        $response->assertOk();
        $response->assertSee('No related cases found.');
    }

    public function test_link_parties_message_when_no_citizens_linked(): void
    {
        $user = User::factory()->create();

        $case = LuponCase::factory()->create([
            'status' => 'filed',
        ]);

        $response = $this->actingAs($user)->get(route('cases.show', $case));

        $response->assertOk();
        $response->assertSee('Link parties to citizens to find related cases.');
    }

    public function test_related_case_links_render_correctly(): void
    {
        $user = User::factory()->create();
        $citizen = Citizen::factory()->create();

        $case = LuponCase::factory()->create([
            'status' => 'filed',
        ]);
        $case->complainants()->attach($citizen->id, ['role' => 'complainant']);

        $relatedCase1 = LuponCase::factory()->create([
            'status' => 'filed',
        ]);
        $relatedCase1->complainants()->attach($citizen->id, ['role' => 'complainant']);

        $relatedCase2 = LuponCase::factory()->create([
            'status' => 'under_mediation',
        ]);
        $relatedCase2->complainants()->attach($citizen->id, ['role' => 'complainant']);

        $response = $this->actingAs($user)->get(route('cases.show', $case));

        $response->assertOk();
        $response->assertSee($relatedCase1->case_number);
        $response->assertSee($relatedCase2->case_number);
        $response->assertSee(route('cases.show', $relatedCase1));
        $response->assertSee(route('cases.show', $relatedCase2));
    }
}
