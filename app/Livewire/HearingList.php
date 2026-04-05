<?php

namespace App\Livewire;

use App\Models\Hearing;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class HearingList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $type = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    #[On('hearingSaved')]
    public function refresh(): void
    {
        // Livewire re-renders automatically on event; this just triggers it
    }

    public function deleteHearing(int $id): void
    {
        Hearing::findOrFail($id)->delete();
        $this->dispatch('hearingSaved'); // refresh calendar
        $this->dispatch('toast', type: 'warning', message: 'Hearing deleted.');
    }

    public function render(): \Illuminate\View\View
    {
        $hearings = Hearing::query()
            ->with(['luponCase'])
            ->whereNotIn('status', ['completed']) // Exclude completed hearings
            ->when($this->search, function ($query) {
                $query->whereHas('luponCase', function ($q) {
                    $q->where('case_number', 'like', '%'.$this->search.'%')
                        ->orWhere('complainant', 'like', '%'.$this->search.'%')
                        ->orWhere('respondent', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->type, fn ($q) => $q->where('hearing_type', $this->type))
            ->orderBy('scheduled_at', 'asc')
            ->paginate(15);

        return view('livewire.hearing-list', compact('hearings'));
    }
}
