<?php

namespace App\Livewire;

use App\Models\LuponCase;
use App\Services\DocumentOrganizer;
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

    public function render(DocumentOrganizer $organizer): \Illuminate\View\View
    {
        $cases = LuponCase::query()
            ->when($this->search, function ($query) {
                $query->where('case_number', 'like', '%'.$this->search.'%')
                    ->orWhere('complainant', 'like', '%'.$this->search.'%')
                    ->orWhere('respondent', 'like', '%'.$this->search.'%')
                    ->orWhereHas('complainants', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    })
                    ->orWhereHas('respondents', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->with(['documents', 'complainants', 'respondents'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.case-list', [
            'cases' => $cases,
        ]);
    }
}
