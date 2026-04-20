<?php

namespace App\Livewire;

use App\Models\Hearing;
use App\Models\LuponMember;
use Livewire\Component;

class HearingAttendance extends Component
{
    public Hearing $hearing;

    public bool $complainant_attended = false;

    public bool $respondent_attended = false;

    public string $member_id = '';

    public string $hearing_status = '';

    public string $minutes = '';

    public string $outcome = '';

    public function mount(Hearing $hearing): void
    {
        $this->hearing = $hearing;

        $complainantRow = $hearing->attendances()->where('party_type', 'complainant')->first();
        $this->complainant_attended = (bool) ($complainantRow?->attended);

        $this->minutes = $hearing->minutes ?? '';
        $this->outcome = $hearing->outcome ?? '';

        $respondentRow = $hearing->attendances()->where('party_type', 'respondent')->first();
        $this->respondent_attended = (bool) ($respondentRow?->attended);

        $this->hearing_status = $hearing->status;
    }

    public function toggleComplainant(): void
    {
        $this->complainant_attended = ! $this->complainant_attended;

        $attendance = $this->hearing->attendances()->firstOrCreate(
            ['party_type' => 'complainant'],
            ['name' => $this->hearing->luponCase->complainant, 'attended' => false]
        );

        $attendance->update(['attended' => $this->complainant_attended]);
        $this->checkStatusConsistency();
        $this->dispatch('toast', type: 'success', message: 'Complainant attendance updated.');
    }

    private function checkStatusConsistency(): void
    {
        if ($this->hearing_status === 'completed' && (! $this->complainant_attended || ! $this->respondent_attended)) {
            $this->hearing_status = $this->hearing->getOriginal('status') ?? 'confirmed';
            if ($this->hearing_status === 'completed') {
                $this->hearing_status = 'confirmed';
            }
            $this->addError('hearing_status', 'Cannot mark as COMPLETED if a party is absent.');
        }
    }

    public function toggleRespondent(): void
    {
        $this->respondent_attended = ! $this->respondent_attended;

        $attendance = $this->hearing->attendances()->firstOrCreate(
            ['party_type' => 'respondent'],
            ['name' => $this->hearing->luponCase->respondent, 'attended' => false]
        );

        $attendance->update(['attended' => $this->respondent_attended]);
        $this->checkStatusConsistency();
        $this->dispatch('toast', type: 'success', message: 'Respondent attendance updated.');
    }

    public function addMember(): void
    {
        $this->validate(['member_id' => 'required|exists:lupon_members,id']);

        if ($this->hearing->attendances()->where('lupon_member_id', $this->member_id)->exists()) {
            $this->addError('member_id', 'This member has already been marked as attending.');

            return;
        }

        $member = LuponMember::findOrFail($this->member_id);

        $this->hearing->attendances()->create([
            'party_type' => 'lupon_member',
            'lupon_member_id' => $member->id,
            'name' => $member->name,
            'attended' => true,
        ]);

        $this->member_id = '';
        $this->hearing->load('attendances.luponMember');
        $this->dispatch('toast', type: 'success', message: 'Lupon member added to attendance.');
    }

    public function removeMember(int $attendanceId): void
    {
        $this->hearing->attendances()->where('id', $attendanceId)->delete();
        $this->hearing->load('attendances.luponMember');
        $this->dispatch('toast', type: 'warning', message: 'Member removed from attendance.');
    }

    public function updateStatus(): void
    {
        $this->validate([
            'hearing_status' => 'required|in:scheduled,confirmed,completed,postponed,failed,cancelled',
            'minutes' => 'nullable|string|max:5000',
            'outcome' => 'nullable|string|max:1000',
        ]);

        if ($this->hearing_status === 'completed' && (! $this->complainant_attended || ! $this->respondent_attended)) {
            $this->addError('hearing_status', 'A hearing can only be marked as COMPLETED if both parties are present.');

            return;
        }

        $this->hearing->update([
            'status' => $this->hearing_status,
            'minutes' => $this->minutes,
            'outcome' => $this->outcome,
        ]);

        $this->dispatch('hearingSaved'); // refresh calendar
        $this->dispatch('toast', type: 'success', message: 'Hearing record updated successfully.');
    }

    public function settleCase(): void
    {
        $case = $this->hearing->luponCase;
        $case->update([
            'status' => 'settled',
        ]);

        session()->flash('success', 'Case has been successfully MARKED AS SETTLED.');
        $this->redirect(route('cases.show', $case), navigate: true);
    }

    public function moveToConciliation(): void
    {
        $this->hearing->luponCase->update([
            'status' => 'under_conciliation',
        ]);

        session()->flash('success', 'Case moved to Conciliation (Pangkat stage).');
        $this->redirect(route('cases.show', $this->hearing->luponCase), navigate: true);
    }

    public function moveToArbitration(): void
    {
        $this->hearing->luponCase->update([
            'status' => 'under_arbitration',
        ]);

        session()->flash('success', 'Case moved to Arbitration. Both parties have consented to Pangkat decision.');
        $this->redirect(route('cases.show', $this->hearing->luponCase), navigate: true);
    }

    public function certifyToCourt(): void
    {
        $case = $this->hearing->luponCase;
        $case->update([
            'status' => 'certified_to_court',
        ]);

        session()->flash('success', 'Case has been CERTIFIED TO COURT.');
        $this->redirect(route('reports.show', ['type' => 'certificate-to-file-action', 'case_id' => $case->id]), navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        $luponMembers = LuponMember::orderBy('name')->get();

        $hasActiveHearing = $this->hearing->luponCase->hearings()
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('id', '!=', $this->hearing->id)
            ->exists();

        return view('livewire.hearing-attendance', compact('luponMembers', 'hasActiveHearing'));
    }
}
