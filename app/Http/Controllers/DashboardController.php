<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCases = \App\Models\LuponCase::count();
        $settledCases = \App\Models\LuponCase::where('status', 'settled')->count();
        $cfaCases = \App\Models\LuponCase::where('status', 'certified_to_court')->count();

        $casesThisMonth = \App\Models\LuponCase::whereMonth('filed_date', now()->month)
            ->whereYear('filed_date', now()->year)
            ->count();

        $recentCases = \App\Models\LuponCase::with(['documents', 'complainants', 'respondents'])
            ->where('filed_date', '>=', now()->subWeek())
            ->latest('filed_date')
            ->take(5)
            ->get();
        $upcomingHearings = \App\Models\Hearing::whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->with(['luponCase.complainants', 'luponCase.respondents'])
            ->get();

        $cfaPreview = \App\Models\LuponCase::where('status', 'certified_to_court')
            ->with(['complainants', 'respondents'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalCases', 'settledCases', 'cfaCases', 'recentCases', 'upcomingHearings',
            'casesThisMonth', 'cfaPreview'
        ));
    }
}
