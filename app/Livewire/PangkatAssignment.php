<?php

namespace App\Livewire;

use App\Models\LuponCase;
use App\Models\LuponMember;
use App\Models\Pangkat;
use Livewire\Component;

class PangkatAssignment extends Component
{
    public LuponCase $case;

    public $chairperson_id = '';

    public $secretary_id = '';

    public $member_id = '';

    public function mount(LuponCase $case)
    {
        $this->case = $case;
        $activePangkat = $case->pangkats()->latest()->first();

        if ($activePangkat) {
            $this->chairperson_id = $activePangkat->chairperson_id ?? '';
            $this->secretary_id = $activePangkat->secretary_id ?? '';
            $this->member_id = $activePangkat->member_id ?? '';
        }
    }

    public function assignPangkat()
    {
        $this->validate([
            'chairperson_id' => 'required|exists:lupon_members,id|different:secretary_id|different:member_id',
            'secretary_id' => 'required|exists:lupon_members,id|different:chairperson_id|different:member_id',
            'member_id' => 'required|exists:lupon_members,id|different:chairperson_id|different:secretary_id',
        ], [
            'different' => 'Members of the Pangkat must be unique.',
        ]);

        Pangkat::create([
            'lupon_case_id' => $this->case->id,
            'chairperson_id' => $this->chairperson_id,
            'secretary_id' => $this->secretary_id,
            'member_id' => $this->member_id,
            'assigned_at' => now(),
        ]);

        $this->case->update(['status' => 'under_conciliation']);
        $this->case->statusHistories()->create([
            'old_status' => $this->case->getOriginal('status') ?? 'filed',
            'new_status' => 'under_conciliation',
            'remarks' => 'Pangkat assigned.',
            'changed_by' => auth()->id() ?? 1,
        ]);

        $this->case->refresh();

        $this->dispatch('toast', type: 'success', message: 'Pangkat assigned successfully.');
    }

    public function render()
    {
        $availableMembers = LuponMember::where('is_active', true)->orderBy('name')->get();

        return view('livewire.pangkat-assignment', [
            'availableMembers' => $availableMembers,
            'currentPangkat' => $this->case->pangkats()->with(['chairperson', 'secretary', 'member'])->latest()->first(),
        ]);
    }
}
