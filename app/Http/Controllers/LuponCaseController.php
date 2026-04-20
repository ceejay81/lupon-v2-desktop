<?php

namespace App\Http\Controllers;

use App\Models\LuponCase;

class LuponCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('cases.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cases.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(LuponCase $case): \Illuminate\View\View
    {
        $case->load([
            'complainants',
            'respondents',
            'pangkats.chairperson',
            'pangkats.secretary',
            'pangkats.member',
            'hearings.attendances',
            'documents',
            'statusHistories.changedBy',
        ]);

        $slaData = $case->sla_data;

        $citizenIds = $case->complainants->pluck('id')->merge($case->respondents->pluck('id'))->unique();
        $citizensLinked = $citizenIds->isNotEmpty();

        if (! $citizensLinked) {
            $relatedCases = collect();
            $relatedCasesTotal = 0;
        } else {
            $relatedCasesQuery = LuponCase::query()
                ->select(['lupon_cases.id', 'case_number', 'nature_of_case', 'status', 'filed_date'])
                ->where(function ($query) use ($citizenIds) {
                    $query->whereHas('complainants', fn ($q) => $q->whereIn('citizens.id', $citizenIds))
                        ->orWhereHas('respondents', fn ($q) => $q->whereIn('citizens.id', $citizenIds));
                })
                ->where('lupon_cases.id', '!=', $case->id)
                ->orderBy('filed_date', 'desc');

            $relatedCasesTotal = $relatedCasesQuery->count();
            $relatedCases = $relatedCasesQuery->limit(5)->get();
        }

        return view('cases.show', compact('case', 'slaData', 'relatedCases', 'relatedCasesTotal', 'citizensLinked'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LuponCase $case)
    {
        return view('cases.edit', compact('case'));
    }

    /**
     * Permanently delete a case.
     */
    public function destroy(LuponCase $case)
    {
        // The user explicitly requested a permanent deletion
        $case->forceDelete();

        return redirect()->route('cases.index')->with('success', 'Case permanently deleted.');
    }

    /**
     * Update case status manually from the view page.
     */
    public function updateStatus(\Illuminate\Http\Request $request, LuponCase $case)
    {
        $request->validate(['status' => 'required|string']);
        $case->status = $request->status;
        $case->save();

        return back()->with('success', 'Case status manually updated.');
    }
}
