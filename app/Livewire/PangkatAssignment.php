<?php

namespace App\Livewire;

use App\Models\LuponCase;
use App\Models\LuponMember;
use App\Models\Pangkat;
use Livewire\Component;

class PangkatAssignment extends Component
{
    public LuponCase $case;

    public array $selectedMemberIds = [];
    public bool $isAssigning = false;

    public function mount(LuponCase $case)
    {
        $this->case = $case;
        $activePangkat = $case->pangkats()->latest()->first();

        if ($activePangkat) {
            $this->selectedMemberIds = $activePangkat->members->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $this->isAssigning = false;
        } else {
            $this->isAssigning = true;
        }
    }

    public function assignPangkat()
    {
        $this->validate([
            'selectedMemberIds' => 'required|array|min:1',
            'selectedMemberIds.*' => 'exists:lupon_members,id',
        ], [
            'selectedMemberIds.required' => 'Please select at least one member.',
        ]);

        $pangkat = Pangkat::create([
            'lupon_case_id' => $this->case->id,
            'assigned_at' => now(),
        ]);

        $pangkat->members()->sync($this->selectedMemberIds);

        $this->case->update(['status' => 'under_conciliation']);
        $this->case->statusHistories()->create([
            'old_status' => $this->case->getOriginal('status') ?? 'filed',
            'new_status' => 'under_conciliation',
            'remarks' => 'Pangkat assigned with ' . count($this->selectedMemberIds) . ' members.',
            'changed_by' => auth()->id() ?? 1,
        ]);

        $this->case->refresh();
        $this->isAssigning = false;

        $this->dispatch('toast', type: 'success', message: 'Pangkat assigned successfully.');
    }

    public function render()
    {
        $availableMembers = LuponMember::where('is_active', true)->orderBy('name')->get();

        return view('livewire.pangkat-assignment', [
            'availableMembers' => $availableMembers,
            'currentPangkat' => $this->case->pangkats()->with('members')->latest()->first(),
        ]);
    }
}
