<?php

namespace App\Livewire;

use App\Models\Citizen;
use App\Models\LuponCase;
use Livewire\Attributes\On;
use Livewire\Component;

class CaseEditForm extends Component
{
    public LuponCase $case;

    public string $complainant = '';

    public string $complainant_address = '';

    public string $complainant_phone = '';

    public ?int $complainant_id = null;

    public string $respondent = '';

    public string $respondent_address = '';

    public string $respondent_phone = '';

    public ?int $respondent_id = null;

    public string $nature_of_case = '';

    public string $description = '';

    public string $filed_date = '';

    public string $status = '';

    public function mount(LuponCase $case): void
    {
        $this->case = $case;
        $this->complainant = $case->complainant;
        $this->complainant_address = $case->complainant_address ?? '';
        $this->complainant_phone = $case->complainant_phone ?? '';
        $this->complainant_id = $case->complainant_id;
        $this->respondent = $case->respondent;
        $this->respondent_address = $case->respondent_address ?? '';
        $this->respondent_phone = $case->respondent_phone ?? '';
        $this->respondent_id = $case->respondent_id;
        $this->nature_of_case = $case->nature_of_case;
        $this->description = $case->description ?? '';
        $this->filed_date = $case->filed_date->format('Y-m-d');
        $this->status = $case->status;
    }

    protected function rules(): array
    {
        return [
            'complainant' => 'required|string|max:255',
            'complainant_address' => 'nullable|string|max:500',
            'complainant_phone' => 'nullable|string|max:11',
            'respondent' => 'required|string|max:255',
            'respondent_address' => 'nullable|string|max:500',
            'respondent_phone' => 'nullable|string|max:11',
            'nature_of_case' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'filed_date' => 'required|date',
        ];
    }

    /** @param array{id: int, name: string, address: string, phone: string, purok: string} $party */
    #[On('partySelected.complainant')]
    public function fillComplainant(array $party): void
    {
        $this->complainant_id = $party['id'];
        $this->complainant = $party['name'];
        $this->complainant_address = $party['address'];
        $this->complainant_phone = $party['phone'];
    }

    /** @param array{id: int, name: string, address: string, phone: string, purok: string} $party */
    #[On('partySelected.respondent')]
    public function fillRespondent(array $party): void
    {
        $this->respondent_id = $party['id'];
        $this->respondent = $party['name'];
        $this->respondent_address = $party['address'];
        $this->respondent_phone = $party['phone'];
    }

    public function submit(): mixed
    {
        $this->validate();

        // Resolve or create citizens
        $complainantCitizen = $this->complainant_id
            ? Citizen::find($this->complainant_id)
            : Citizen::firstOrCreate(
                ['name' => $this->complainant],
                ['address' => $this->complainant_address ?: null, 'phone' => $this->complainant_phone ?: null]
            );

        $respondentCitizen = $this->respondent_id
            ? Citizen::find($this->respondent_id)
            : Citizen::firstOrCreate(
                ['name' => $this->respondent],
                ['address' => $this->respondent_address ?: null, 'phone' => $this->respondent_phone ?: null]
            );

        $this->case->update([
            'complainant' => $this->complainant,
            'complainant_address' => $this->complainant_address,
            'complainant_phone' => $this->complainant_phone,
            'complainant_id' => $complainantCitizen?->id,
            'respondent' => $this->respondent,
            'respondent_address' => $this->respondent_address,
            'respondent_phone' => $this->respondent_phone,
            'respondent_id' => $respondentCitizen?->id,
            'nature_of_case' => $this->nature_of_case,
            'description' => $this->description,
            'filed_date' => $this->filed_date,
        ]);

        session()->flash('message', 'Case successfully updated.');

        return redirect()->route('cases.show', $this->case);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.case-edit-form');
    }
}
