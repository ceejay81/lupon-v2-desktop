<?php

namespace App\Livewire\DigitalBlotter;

use App\Models\Citizen;
use Livewire\Component;

class PartySearch extends Component
{
    /** 'complainant' or 'respondent' */
    public string $partyType = 'complainant';

    public string $search = '';

    /** @var array<int, array{id: int, name: string, address: string, phone: string, purok: string}> */
    public array $results = [];

    public bool $showDropdown = false;

    /** Inline create form */
    public bool $showCreateForm = false;
    public string $newName    = '';
    public string $newAddress = '';
    public string $newPhone   = '';
    public string $newPurok   = '';

    public function updatedSearch(): void
    {
        $this->showCreateForm = false;
        $query = trim($this->search);

        if (strlen($query) < 2) {
            $this->results      = [];
            $this->showDropdown = false;

            return;
        }

        $this->results = Citizen::where('name', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'address', 'phone', 'purok'])
            ->map(fn ($c) => [
                'id'      => $c->id,
                'name'    => $c->name,
                'address' => $c->address ?? '',
                'phone'   => $c->phone ?? '',
                'purok'   => $c->purok ?? '',
            ])
            ->all();

        $this->showDropdown = true;
    }

    public function selectCitizen(int $index): void
    {
        $citizen = $this->results[$index] ?? null;

        if (! $citizen) {
            return;
        }

        $this->dispatch("partySelected.{$this->partyType}", party: $citizen);

        $this->search         = $citizen['name'];
        $this->results        = [];
        $this->showDropdown   = false;
        $this->showCreateForm = false;
    }

    public function openCreateForm(): void
    {
        $this->showDropdown   = false;
        $this->showCreateForm = true;
        $this->newName        = $this->search;
        $this->newAddress     = '';
        $this->newPhone       = '';
        $this->newPurok       = '';
    }

    public function createCitizen(): void
    {
        $this->validate([
            'newName'    => 'required|string|max:255',
            'newAddress' => 'nullable|string|max:500',
            'newPhone'   => 'nullable|string|max:11',
            'newPurok'   => 'nullable|string|max:100',
        ]);

        $citizen = Citizen::create([
            'name'    => trim($this->newName),
            'address' => trim($this->newAddress) ?: null,
            'phone'   => trim($this->newPhone) ?: null,
            'purok'   => trim($this->newPurok) ?: null,
        ]);

        $party = [
            'id'      => $citizen->id,
            'name'    => $citizen->name,
            'address' => $citizen->address ?? '',
            'phone'   => $citizen->phone ?? '',
            'purok'   => $citizen->purok ?? '',
        ];

        $this->dispatch("partySelected.{$this->partyType}", party: $party);

        $this->search         = $citizen->name;
        $this->showCreateForm = false;
        $this->newName        = '';
        $this->newAddress     = '';
        $this->newPhone       = '';
        $this->newPurok       = '';
    }

    public function cancelCreate(): void
    {
        $this->showCreateForm = false;
    }

    public function clearDropdown(): void
    {
        $this->showDropdown = false;
    }

    public function render(): \Illuminate\View\View
    {
        return view('components.digital-blotter.party-search');
    }
}
