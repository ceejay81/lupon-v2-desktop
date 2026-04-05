<?php

namespace App\Livewire;

use App\Models\Hearing;
use App\Models\LuponCase;
use Livewire\Attributes\On;
use Livewire\Component;

class HearingForm extends Component
{
    public ?int $case_id = null;

    /** When editing an existing hearing */
    public ?int $hearingId = null;

    public string $hearing_type = '';

    public string $scheduled_at = '';

    public string $location = 'Barangay Hall';

    public string $minutes = '';

    public string $outcome = '';

    public bool $showModal = false;

    public const TYPES = [
        'mediation' => 'Mediation',
        'conciliation' => 'Conciliation',
        'arbitration' => 'Arbitration',
    ];

    public function mount(?int $case_id = null): void
    {
        $this->case_id = $case_id;
    }

    /** Open modal for a new hearing (optionally pre-filled with case) */
    #[On('openHearingForm')]
    public function openNew(?int $caseId = null): void
    {
        $this->reset(['hearingId', 'hearing_type', 'scheduled_at', 'minutes', 'outcome']);
        $this->location = 'Barangay Hall';

        if ($caseId) {
            $this->case_id = $caseId;
        }

        $this->showModal = true;
    }

    /** Open modal pre-filled for editing */
    #[On('editHearing')]
    public function openEdit(int $hearingId): void
    {
        $hearing = Hearing::findOrFail($hearingId);

        $this->hearingId = $hearing->id;
        $this->case_id = $hearing->lupon_case_id;
        $this->hearing_type = $hearing->hearing_type;
        $this->scheduled_at = $hearing->scheduled_at->format('Y-m-d\TH:i');
        $this->location = $hearing->location;
        $this->minutes = $hearing->minutes ?? '';
        $this->outcome = $hearing->outcome ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'case_id' => ['required', 'exists:lupon_cases,id'],
            'hearing_type' => ['required', 'in:mediation,conciliation,arbitration'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'minutes' => ['nullable', 'string'],
            'outcome' => ['nullable', 'string', 'max:500'],
        ]);

        $data = [
            'lupon_case_id' => $this->case_id,
            'hearing_type' => $this->hearing_type,
            'scheduled_at' => $this->scheduled_at,
            'location' => $this->location ?: 'Barangay Hall',
            'minutes' => $this->minutes ?: null,
            'outcome' => $this->outcome ?: null,
        ];

        if ($this->hearingId) {
            Hearing::findOrFail($this->hearingId)->update($data);
            $message = 'Hearing updated successfully.';
        } else {
            $data['status'] = 'scheduled';
            Hearing::create($data);
            $message = 'Hearing scheduled successfully.';
        }

        $this->showModal = false;
        $this->dispatch('hearingSaved');
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        $cases = LuponCase::orderByDesc('filed_date')
            ->whereNotIn('status', ['archived', 'dismissed', 'settled', 'certified_to_court'])
            ->get(['id', 'case_number', 'complainant', 'respondent']);

        return view('livewire.hearing-form', compact('cases'));
    }
}
