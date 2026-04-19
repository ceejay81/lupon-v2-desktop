<?php

namespace Tests\Feature;

use App\Models\Citizen;
use App\Models\LuponCase;
use Eris\Attributes\ErisRepeat;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

// Feature: case-show-page-improvements, Property 4: Related cases query correctness
class RelatedCasesQueryTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Build the related cases query the same way the controller does.
     *
     * @return Collection<int, LuponCase>
     */
    private function fetchRelatedCases(LuponCase $case): Collection
    {
        $citizenIds = $case->complainants->pluck('id')->merge($case->respondents->pluck('id'))->unique();

        if ($citizenIds->isEmpty()) {
            return collect();
        }

        return LuponCase::query()
            ->select(['lupon_cases.id', 'case_number', 'nature_of_case', 'status', 'filed_date'])
            ->where(function ($query) use ($citizenIds) {
                $query->whereHas('complainants', fn ($q) => $q->whereIn('citizens.id', $citizenIds))
                    ->orWhereHas('respondents', fn ($q) => $q->whereIn('citizens.id', $citizenIds));
            })
            ->where('lupon_cases.id', '!=', $case->id)
            ->orderBy('filed_date', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Property 4: Related cases query correctness
     * Validates: Requirements 3.1, 3.6
     *
     * For any LuponCase with a non-null complainant_id or respondent_id, the
     * related cases collection must contain exactly those other cases that share
     * at least one of those citizen IDs, and must never contain the current case.
     */
    #[ErisRepeat(100)]
    public function test_related_cases_contains_exactly_correct_cases_and_never_current_case(): void
    {
        $this->forAll(
            Generators::choose(1, 4),  // number of related cases
            Generators::choose(0, 3)   // number of unrelated cases
        )->then(function (int $relatedCount, int $unrelatedCount) {
            $complainantCitizen = Citizen::factory()->create();
            $respondentCitizen = Citizen::factory()->create();

            /** @var LuponCase $focalCase */
            $focalCase = LuponCase::factory()->create([
                'filed_date' => now()->subDays(10),
            ]);
            $focalCase->complainants()->attach($complainantCitizen->id, ['role' => 'complainant']);
            $focalCase->respondents()->attach($respondentCitizen->id, ['role' => 'respondent']);

            // Create related cases — each shares complainant_id or respondent_id with focal case
            $relatedIds = [];
            for ($i = 0; $i < $relatedCount; $i++) {
                $useComplainant = ($i % 2 === 0);
                $related = LuponCase::factory()->create([
                    'filed_date' => now()->subDays(20 + $i),
                ]);
                if ($useComplainant) {
                    $related->complainants()->attach($complainantCitizen->id, ['role' => 'complainant']);
                } else {
                    $related->respondents()->attach($respondentCitizen->id, ['role' => 'respondent']);
                }
                $relatedIds[] = $related->id;
            }

            // Create unrelated cases — different citizens, no overlap
            for ($i = 0; $i < $unrelatedCount; $i++) {
                $otherCitizen = Citizen::factory()->create();
                $unrelated = LuponCase::factory()->create([
                    'filed_date' => now()->subDays(5 + $i),
                ]);
                $unrelated->complainants()->attach($otherCitizen->id, ['role' => 'complainant']);
            }

            $result = $this->fetchRelatedCases($focalCase);

            // Must never contain the current case
            $this->assertFalse(
                $result->contains('id', $focalCase->id),
                'Related cases must not contain the current case itself'
            );

            // Every returned ID must be a known related case (not an unrelated one)
            foreach ($result as $returned) {
                $this->assertContains(
                    $returned->id,
                    $relatedIds,
                    "Returned case {$returned->id} is not a related case"
                );
            }

            // All related cases (up to 5) must appear in the result
            // Since relatedCount <= 4, all should be present
            foreach ($relatedIds as $relatedId) {
                $this->assertTrue(
                    $result->contains('id', $relatedId),
                    "Related case {$relatedId} is missing from the result"
                );
            }
        });
    }

    /**
     * When both citizen IDs are null, the query is skipped and an empty collection is returned.
     */
    public function test_skips_query_when_both_citizen_ids_are_null(): void
    {
        /** @var LuponCase $case */
        $case = LuponCase::factory()->create([
            'complainant_id' => null,
            'respondent_id' => null,
        ]);

        LuponCase::factory()->count(3)->create();

        $result = $this->fetchRelatedCases($case);

        $this->assertCount(0, $result);
    }
}
