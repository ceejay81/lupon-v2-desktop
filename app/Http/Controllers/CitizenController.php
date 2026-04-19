<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $citizens = Citizen::withCount([
            'casesAsComplainant',
            'casesAsRespondent',
        ])
            ->orderBy('name')
            ->paginate(20);

        return view('citizens.index', compact('citizens'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('citizens.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'purok' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:11'],
        ]);

        $citizen = Citizen::create($data);

        return redirect()->route('citizens.show', $citizen)
            ->with('message', 'Citizen registered successfully.');
    }

    public function show(Citizen $citizen): \Illuminate\View\View
    {
        $citizen->load([
            'casesAsComplainant' => fn ($q) => $q->orderByDesc('filed_date')
                ->with(['hearings' => fn ($h) => $h->where('scheduled_at', '>=', now())
                    ->where('status', 'scheduled')->orderBy('scheduled_at')->limit(1)]),
            'casesAsRespondent' => fn ($q) => $q->orderByDesc('filed_date')
                ->with(['hearings' => fn ($h) => $h->where('scheduled_at', '>=', now())
                    ->where('status', 'scheduled')->orderBy('scheduled_at')->limit(1)]),
        ]);

        return view('citizens.show', compact('citizen'));
    }

    public function edit(Citizen $citizen): \Illuminate\View\View
    {
        return view('citizens.edit', compact('citizen'));
    }

    public function update(Request $request, Citizen $citizen): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'purok' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:11'],
        ]);

        $citizen->update($data);

        return redirect()->route('citizens.show', $citizen)
            ->with('message', 'Citizen updated successfully.');
    }

    public function destroy(Citizen $citizen): \Illuminate\Http\RedirectResponse
    {
        // The user explicitly requested a permanent deletion
        if (method_exists($citizen, 'forceDelete')) {
            $citizen->forceDelete();
        } else {
            $citizen->delete();
        }

        return redirect()->route('citizens.index')
            ->with('message', 'Citizen permanently deleted.');
    }
}
