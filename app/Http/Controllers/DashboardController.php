<?php

namespace App\Http\Controllers;

use App\Models\Hearing;
use App\Models\LuponCase;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Cache duration for statistics (in seconds).
     */
    private const CACHE_TTL = 300; // 5 minutes

    public function index()
    {
        // Cache expensive count queries
        $stats = Cache::remember('dashboard_stats', self::CACHE_TTL, function () {
            return [
                'totalCases' => LuponCase::count(),
                'settledCases' => LuponCase::where('status', 'settled')->count(),
                'cfaCases' => LuponCase::where('status', 'certified_to_court')->count(),
                'casesThisMonth' => LuponCase::whereMonth('filed_date', now()->month)
                    ->whereYear('filed_date', now()->year)
                    ->count(),
            ];
        });

        // Recent cases with selective loading
        $recentCases = LuponCase::select([
            'id',
            'case_number',
            'complainant',
            'respondent',
            'status',
            'filed_date',
            'nature_of_case',
            'created_at',
        ])
            ->where('filed_date', '>=', now()->subWeek())
            ->withCount('documents')
            ->with([
                'complainants' => fn ($q) => $q->select('citizens.id', 'citizens.name'),
                'respondents' => fn ($q) => $q->select('citizens.id', 'citizens.name'),
            ])
            ->latest('filed_date')
            ->take(5)
            ->get();

        // Upcoming hearings with optimized relationships
        $upcomingHearings = Hearing::whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->with([
                'luponCase' => fn ($q) => $q->select('id', 'case_number', 'complainant', 'respondent', 'status'),
            ])
            ->get();

        // CFA preview - minimal data
        $cfaPreview = LuponCase::select(['id', 'case_number', 'complainant', 'respondent', 'status', 'created_at'])
            ->where('status', 'certified_to_court')
            ->with([
                'complainants' => fn ($q) => $q->select('citizens.id', 'citizens.name'),
                'respondents' => fn ($q) => $q->select('citizens.id', 'citizens.name'),
            ])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', array_merge($stats, compact(
            'recentCases',
            'upcomingHearings',
            'cfaPreview'
        )));
    }
}
