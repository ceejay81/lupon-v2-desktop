<?php

namespace App\Livewire;

use App\Models\Citizen;
use App\Models\LuponCase;
use App\Services\CaseNumberGenerator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class CaseForm extends Component
{
    public array $complainants = [['id' => null, 'name' => '', 'address' => '', 'phone' => '']];

    public array $respondents = [['id' => null, 'name' => '', 'address' => '', 'phone' => '']];

    public string $nature_of_case = '';

    public string $description = '';

    public string $filed_date = '';

    public string $case_number = '';

    // Additional blotter fields
    public string $date_of_service_summon = '';

    public string $remarks = '';

    public const NATURES = [
        'property_dispute' => 'Property Dispute',
        'collection_of_debt' => 'Collection of Debt',
        'boundary_issue' => 'Boundary Issue',
        'noise_complaint' => 'Noise Complaint',
        'verbal_abuse' => 'Verbal Abuse',
        'physical_altercation' => 'Physical Altercation',
        'domestic_issue' => 'Domestic Issue',
        'others' => 'Others',
    ];

    protected function rules(): array
    {
        return [
            'case_number' => 'required|string|max:50|unique:lupon_cases,case_number',
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
            'date_of_service_summon' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function mount(CaseNumberGenerator $generator): void
    {
        $this->filed_date = now()->format('Y-m-d');
        $this->case_number = $generator->generate();
    }

    public function addComplainant()
    {
        $this->complainants[] = ['id' => null, 'name' => '', 'address' => '', 'phone' => ''];
    }

    public function removeComplainant($index)
    {
        unset($this->complainants[$index]);
        $this->complainants = array_values($this->complainants); // Re-index
    }

    public function addRespondent()
    {
        $this->respondents[] = ['id' => null, 'name' => '', 'address' => '', 'phone' => ''];
    }

    public function removeRespondent($index)
    {
        unset($this->respondents[$index]);
        $this->respondents = array_values($this->respondents); // Re-index
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

    public function submit(CaseNumberGenerator $generator): mixed
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

            // Create Case
            $case = LuponCase::create([
                'case_number' => $this->case_number,
                // Save legacy fields as comma separated for backwards compatibility
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
                'date_of_service_summon' => $this->date_of_service_summon ?: null,
                'remarks' => $this->remarks ?: null,
                'status' => 'filed',
                'filed_by' => auth()->id(),
            ]);

            // Attach pivot records
            foreach ($complainantIds as $cId) {
                $case->complainants()->attach($cId, ['role' => 'complainant']);
            }
            foreach ($respondentIds as $rId) {
                $case->respondents()->attach($rId, ['role' => 'respondent']);
            }

            $case->statusHistories()->create([
                'old_status' => 'new',
                'new_status' => 'filed',
                'remarks' => 'Case filed.',
                'changed_by' => auth()->id(),
            ]);
        });

        session()->flash('message', 'Case successfully filed.');

        return redirect()->route('cases.index');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.case-form');
    }
}
