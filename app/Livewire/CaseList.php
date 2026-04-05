<?php

namespace App\Livewire;

use App\Models\LuponCase;
use App\Services\DocumentOrganizer;
use Livewire\Component;
use Livewire\WithPagination;

class CaseList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }



    public function render(DocumentOrganizer $organizer): \Illuminate\View\View
    {
        $cases = LuponCase::query()
            ->when($this->search, function ($query) {
                $query->where('case_number', 'like', '%'.$this->search.'%')
                    ->orWhere('complainant', 'like', '%'.$this->search.'%')
                    ->orWhere('respondent', 'like', '%'.$this->search.'%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->with('documents')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.case-list', [
            'cases' => $cases,
        ]);
    }
}
