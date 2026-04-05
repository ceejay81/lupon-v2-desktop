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

// Feature: case-show-page-improvements, Property 5: Related cases limited to 5 most recent
class RelatedCasesLimitTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Build the related cases query and total count the same way the controller does.
     *
     * @return array{collection: Collection<int, LuponCase>, total: int}
     */
    private function fetchRelatedCasesWithTotal(LuponCase $case): array
    {
        if ($case->complainant_id === null && $case->respondent_id === null) {
            return ['collection' => collect(), 'total' => 0];
        }

        $query = LuponCase::query()
            ->select(['id', 'case_number', 'nature_of_case', 'status', 'filed_date'])
            ->where(function ($q) use ($case) {
                $q->where('complainant_id', $case->complainant_id)
                    ->orWhere('respondent_id', $case->respondent_id);
            })
            ->where('id', '!=', $case->id)
            ->orderBy('filed_date', 'desc');

        $total = $query->count();
        $collection = $query->limit(5)->get();

        return ['collection' => $collection, 'total' => $total];
    }

    /**
     * Property 5: Related cases limited to 5 most recent
     * Validates: Requirements 3.8
     *
     * For any LuponCase that has more than 5 related cases, the returned
     * collection must contain exactly 5 items ordered by filed_date descending,
     * and the accompanying total count must equal the true number of related cases.
     */
    #[ErisRepeat(100)]
    public function testRelatedCasesLimitedToFiveMostRecentWithAccurateTotal(): void
    {
        $this->forAll(
            Generators::choose(6, 20)  // number of related cases (always > 5)
        )->then(function (int $relatedCount) {
            $complainantCitizen = Citizen::factory()->create();
            $respondentCitizen = Citizen::factory()->create();

            /** @var LuponCase $focalCase */
            $focalCase = LuponCase::factory()->create([
                'complainant_id' => $complainantCitizen->id,
                'respondent_id'  => $respondentCitizen->id,
                'filed_date'     => now()->subDays(100),
            ]);

            // Create related cases with distinct filed_dates so ordering is deterministic
            $createdCases = [];
            for ($i = 0; $i < $relatedCount; $i++) {
                $createdCases[] = LuponCase::factory()->create([
                    'complainant_id' => $complainantCitizen->id,
                    'respondent_id'  => null,
                    'filed_date'     => now()->subDays($relatedCount - $i + 1),
                ]);
            }

            $result = $this->fetchRelatedCasesWithTotal($focalCase);
            $collection = $result['collection'];
            $total = $result['total'];

            // Collection must have exactly 5 items
            $this->assertCount(5, $collection, "Expected exactly 5 related cases, got {$collection->count()}");

            // Total must equal the true number of related cases
            $this->assertSame(
                $relatedCount,
                $total,
                "Total count {$total} does not match true related count {$relatedCount}"
            );

            // Collection must be ordered by filed_date descending
            $dates = $collection->pluck('filed_date')->map(fn ($d) => (string) $d)->values()->toArray();
            $sortedDates = $dates;
            rsort($sortedDates);
            $this->assertSame(
                $sortedDates,
                $dates,
                'Related cases are not ordered by filed_date descending'
            );

            // The 5 returned cases must be the 5 most recently filed
            $expectedTopFive = collect($createdCases)
                ->sortByDesc('filed_date')
                ->take(5)
                ->pluck('id')
                ->sort()
                ->values()
                ->toArray();

            $returnedIds = $collection->pluck('id')->sort()->values()->toArray();

            $this->assertSame(
                $expectedTopFive,
                $returnedIds,
                'The 5 returned cases are not the 5 most recently filed'
            );
        });
    }
}
