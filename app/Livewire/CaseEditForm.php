<?php

namespace App\Livewire;

use App\Models\Citizen;
use App\Models\LuponCase;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class CaseEditForm extends Component
{
    public LuponCase $case;

    public array $complainants = [];

    public array $respondents = [];

    public string $nature_of_case = '';

    public string $description = '';

    public string $filed_date = '';

    public string $case_number = '';

    public function mount(LuponCase $case): void
    {
        $this->case = $case;
        $this->case_number = $case->case_number;
        $this->nature_of_case = $case->nature_of_case;
        $this->description = $case->description ?? '';
        $this->filed_date = $case->filed_date->format('Y-m-d');

        // Load existing complainants
        $complainants = $case->complainants;
        if ($complainants->isNotEmpty()) {
            foreach ($complainants as $c) {
                $this->complainants[] = [
                    'id' => $c->id,
                    'name' => $c->name,
                    'address' => $c->address ?? '',
                    'phone' => $c->phone ?? '',
                ];
            }
        } else {
            // Fallback to legacy fields if pivot is empty (for transition)
            $this->complainants[] = [
                'id' => $case->complainant_id,
                'name' => $case->complainant,
                'address' => $case->complainant_address ?? '',
                'phone' => $case->complainant_phone ?? '',
            ];
        }

        // Load existing respondents
        $respondents = $case->respondents;
        if ($respondents->isNotEmpty()) {
            foreach ($respondents as $r) {
                $this->respondents[] = [
                    'id' => $r->id,
                    'name' => $r->name,
                    'address' => $r->address ?? '',
                    'phone' => $r->phone ?? '',
                ];
            }
        } else {
            // Fallback for transition
            $this->respondents[] = [
                'id' => $case->respondent_id,
                'name' => $case->respondent,
                'address' => $case->respondent_address ?? '',
                'phone' => $case->respondent_phone ?? '',
            ];
        }
    }

    protected function rules(): array
    {
        return [
            'complainants' => 'required|array|min:1',
            'complainants.*.name' => 'required|string|max:255',
            'complainants.*.address' => 'nullable|string|max:500',
            'complainants.*.phone' => 'nullable|string|max:11',
            'respondents' => 'required|array|min:1',
            'respondents.*.name' => 'required|string|max:255',
            'respondents.*.address' => 'nullable|string|max:500',
            'respondents.*.phone' => 'nullable|string|max:11',
            'nature_of_case' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'filed_date' => 'required|date',
        ];
    }

    public function addComplainant()
    {
        $this->complainants[] = ['id' => null, 'name' => '', 'address' => '', 'phone' => ''];
    }

    public function removeComplainant($index)
    {
        unset($this->complainants[$index]);
        $this->complainants = array_values($this->complainants);
    }

    public function addRespondent()
    {
        $this->respondents[] = ['id' => null, 'name' => '', 'address' => '', 'phone' => ''];
    }

    public function removeRespondent($index)
    {
        unset($this->respondents[$index]);
        $this->respondents = array_values($this->respondents);
    }

    /** @param array{id: int, name: string, address: string, phone: string, purok: string, index: int|null} $party */
    #[On('partySelected.complainant')]
    public function fillComplainant(array $party, int $index = 0): void
    {
        if (isset($this->complainants[$index])) {
            $this->complainants[$index]['id'] = $party['id'];
            $this->complainants[$index]['name'] = $party['name'];
            $this->complainants[$index]['address'] = $party['address'];
            $this->complainants[$index]['phone'] = $party['phone'];
        }
    }

    /** @param array{id: int, name: string, address: string, phone: string, purok: string, index: int|null} $party */
    #[On('partySelected.respondent')]
    public function fillRespondent(array $party, int $index = 0): void
    {
        if (isset($this->respondents[$index])) {
            $this->respondents[$index]['id'] = $party['id'];
            $this->respondents[$index]['name'] = $party['name'];
            $this->respondents[$index]['address'] = $party['address'];
            $this->respondents[$index]['phone'] = $party['phone'];
        }
    }

    public function submit(): mixed
    {
        $this->validate();

        DB::transaction(function () {
            $complainantNames = [];
            $complainantIds = [];
            foreach ($this->complainants as $comp) {
                if (trim($comp['name']) === '') {
                    continue;
                }
                $citizen = $comp['id'] ? Citizen::find($comp['id']) : Citizen::firstOrCreate(
                    ['name' => collect(explode(' ', $comp['name']))->map(fn ($w) => ucfirst(strtolower($w)))->join(' ')],
                    ['address' => $comp['address'] ?: null, 'phone' => $comp['phone'] ?: null]
                );
                $complainantIds[] = $citizen->id;
                $complainantNames[] = $citizen->name;
            }

            $respondentNames = [];
            $respondentIds = [];
            foreach ($this->respondents as $resp) {
                if (trim($resp['name']) === '') {
                    continue;
                }
                $citizen = $resp['id'] ? Citizen::find($resp['id']) : Citizen::firstOrCreate(
                    ['name' => collect(explode(' ', $resp['name']))->map(fn ($w) => ucfirst(strtolower($w)))->join(' ')],
                    ['address' => $resp['address'] ?: null, 'phone' => $resp['phone'] ?: null]
                );
                $respondentIds[] = $citizen->id;
                $respondentNames[] = $citizen->name;
            }

            // Update Case
            $this->case->update([
                'complainant' => implode(', ', $complainantNames),
                'complainant_address' => $this->complainants[0]['address'] ?? null,
                'complainant_phone' => $this->complainants[0]['phone'] ?? null,
                'complainant_id' => $complainantIds[0] ?? null,

                'respondent' => implode(', ', $respondentNames),
                'respondent_address' => $this->respondents[0]['address'] ?? null,
                'respondent_phone' => $this->respondents[0]['phone'] ?? null,
                'respondent_id' => $respondentIds[0] ?? null,

                'nature_of_case' => $this->nature_of_case,
                'description' => $this->description,
                'filed_date' => $this->filed_date,
            ]);

            // Sync pivot records
            $this->case->complainants()->syncWithPivotValues($complainantIds, ['role' => 'complainant']);
            $this->case->respondents()->syncWithPivotValues($respondentIds, ['role' => 'respondent']);
        });

        session()->flash('message', 'Case successfully updated.');

        return redirect()->route('cases.show', $this->case);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.case-edit-form');
    }
}
