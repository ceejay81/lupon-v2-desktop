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
    public string $complainant         = '';
    public string $complainant_address = '';
    public string $complainant_phone   = '';
    public ?int   $complainant_id      = null;

    public string $respondent         = '';
    public string $respondent_address = '';
    public string $respondent_phone   = '';
    public ?int   $respondent_id      = null;

    public string $nature_of_case = '';
    public string $description    = '';
    public string $filed_date     = '';
    public string $case_number    = '';
    
    // Additional blotter fields
    public string $date_of_service_summon = '';
    public string $remarks = '';

    public const NATURES = [
        'property_dispute'     => 'Property Dispute',
        'collection_of_debt'   => 'Collection of Debt',
        'boundary_issue'       => 'Boundary Issue',
        'noise_complaint'      => 'Noise Complaint',
        'verbal_abuse'         => 'Verbal Abuse',
        'physical_altercation' => 'Physical Altercation',
        'domestic_issue'       => 'Domestic Issue',
        'others'               => 'Others',
    ];

    protected function rules(): array
    {
        return [
            'case_number'         => 'required|string|max:50|unique:lupon_cases,case_number',
            'complainant'         => 'required|string|max:255',
            'complainant_address' => 'nullable|string|max:500',
            'complainant_phone'   => 'nullable|string|max:11',
            'respondent'          => 'required|string|max:255',
            'respondent_address'  => 'nullable|string|max:500',
            'respondent_phone'    => 'nullable|string|max:11',
            'nature_of_case'      => 'required|string|max:255', // Changed to allow free text
            'description'         => 'nullable|string|max:2000',
            'filed_date'          => 'required|date',
            'date_of_service_summon' => 'nullable|date',
            'remarks'             => 'nullable|string|max:1000',
        ];
    }

    public function mount(CaseNumberGenerator $generator): void
    {
        $this->filed_date = now()->format('Y-m-d');
        $this->case_number = $generator->generate();
    }

    /** @param array{id: int, name: string, address: string, phone: string, purok: string} $party */
    #[On('partySelected.complainant')]
    public function fillComplainant(array $party): void
    {
        $this->complainant_id      = $party['id'];
        $this->complainant         = $party['name'];
        $this->complainant_address = $party['address'];
        $this->complainant_phone   = $party['phone'];
    }

    /** @param array{id: int, name: string, address: string, phone: string, purok: string} $party */
    #[On('partySelected.respondent')]
    public function fillRespondent(array $party): void
    {
        $this->respondent_id      = $party['id'];
        $this->respondent         = $party['name'];
        $this->respondent_address = $party['address'];
        $this->respondent_phone   = $party['phone'];
    }

    public function submit(CaseNumberGenerator $generator): mixed
    {
        $this->validate();

        DB::transaction(function () use ($generator) {
            // Resolve or create complainant citizen
            $complainantCitizen = $this->complainant_id
                ? Citizen::find($this->complainant_id)
                : Citizen::firstOrCreate(
                    ['name' => $this->complainant],
                    ['address' => $this->complainant_address ?: null, 'phone' => $this->complainant_phone ?: null]
                );

            // Resolve or create respondent citizen
            $respondentCitizen = $this->respondent_id
                ? Citizen::find($this->respondent_id)
                : Citizen::firstOrCreate(
                    ['name' => $this->respondent],
                    ['address' => $this->respondent_address ?: null, 'phone' => $this->respondent_phone ?: null]
                );

            $case = LuponCase::create([
                'case_number'         => $this->case_number,
                'complainant'         => $this->complainant,
                'complainant_address' => $this->complainant_address,
                'complainant_phone'   => $this->complainant_phone,
                'complainant_id'      => $complainantCitizen?->id,
                'respondent'          => $this->respondent,
                'respondent_address'  => $this->respondent_address,
                'respondent_phone'    => $this->respondent_phone,
                'respondent_id'       => $respondentCitizen?->id,
                'nature_of_case'      => $this->nature_of_case,
                'description'         => $this->description,
                'filed_date'          => $this->filed_date,
                'date_of_service_summon' => $this->date_of_service_summon ?: null,
                'remarks'             => $this->remarks ?: null,
                'status'              => 'filed',
                'filed_by'            => auth()->id(),
            ]);

            $case->statusHistories()->create([
                'old_status' => 'new',
                'new_status' => 'filed',
                'remarks'    => 'Case filed.',
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
