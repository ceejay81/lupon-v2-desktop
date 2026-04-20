<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard with statistics.
     */
    public function index(): \Illuminate\View\View
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $totalCases = \App\Models\LuponCase::count();
        $settledCases = \App\Models\LuponCase::where('status', 'settled')->count();
        $ongoingCases = \App\Models\LuponCase::whereIn('status', ['under_mediation', 'under_conciliation', 'under_arbitration'])->count();
        $cfaCases = \App\Models\LuponCase::where('status', 'certified_to_court')->count();

        return view('reports.index', compact(
            'totalCases', 'settledCases', 'ongoingCases', 'cfaCases', 'currentYear', 'currentMonth'
        ));
    }

    /**
     * Redirect report type requests back to the reports index in V2.
     * Template rendering has been removed — documents are uploaded externally.
     */
    public function show(string $type, \Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('reports.index')->with(
            'info',
            'Document templates are not available in Version 2. Please upload your documents using the Case Documents tab.'
        );
    }
}
