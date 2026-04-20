<?php

namespace App\Livewire;

use App\Models\LuponCase;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CaseList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-5';

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    // Debounce search to reduce query load
    protected $queryString = [
        'search' => ['except' => '', 'as' => 's'],
        'status' => ['except' => '', 'as' => 'st'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updateCaseStatus(int $caseId, string $newStatus): void
    {
        $case = LuponCase::find($caseId);
        if ($case) {
            $case->status = $newStatus;
            $case->save();
        }
    }

    /**
     * Get optimized case query with selective loading.
     */
    private function getCaseQuery()
    {
        return LuponCase::query()
            ->select([
                'id',
                'case_number',
                'complainant',
                'respondent',
                'status',
                'filed_date',
                'nature_of_case',
                'created_at',
                'updated_at',
            ])
            ->when($this->search, function ($query) {
                $searchTerm = $this->search;

                // Use more efficient search - only exact prefix can use index
                // For full-text search, we'll use a separate approach
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('case_number', 'like', $searchTerm.'%')
                        ->orWhere('case_number', 'like', '%'.$searchTerm.'%')
                        ->orWhere('complainant', 'like', '%'.$searchTerm.'%')
                        ->orWhere('respondent', 'like', '%'.$searchTerm.'%');
                });

                // Only search citizens if main fields don't match (prevents heavy joins)
                if (strlen($searchTerm) >= 3) {
                    $query->orWhereHas('complainants', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%'.$searchTerm.'%');
                    });
                    $query->orWhereHas('respondents', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%'.$searchTerm.'%');
                    });
                }
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            // Selective eager loading: only load counts and minimal data
            ->withCount(['documents', 'hearings'])
            ->with([
                'complainants' => fn ($q) => $q->select('citizens.id', 'citizens.name'),
                'respondents' => fn ($q) => $q->select('citizens.id', 'citizens.name'),
            ]);
    }

    /**
     * Get cached status counts for filter dropdown.
     */
    public function getStatusCountsProperty(): array
    {
        return Cache::remember('case_status_counts', 300, function () {
            return LuponCase::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    public function render(): \Illuminate\View\View
    {
        $cases = $this->getCaseQuery()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.case-list', [
            'cases' => $cases,
            'statusCounts' => $this->statusCounts,
        ]);
    }
}
