<?php

namespace App\Http\Controllers;

class FolderizedReportController extends Controller
{
    /**
     * Display the folderized report archive.
     * Template discovery has been removed in V2 — only case and report archives are shown.
     */
    public function index(): \Illuminate\View\View
    {
        // 1. Fetch finalized reports
        $finalizedReports = \App\Models\Report::where('status', 'finalized')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // 2. Fetch settled and certified cases (Limited to last 24 months for performance)
        $cutoffDate = now()->subMonths(24);
        $casesDataQuery = \App\Models\LuponCase::whereIn('status', ['settled', 'certified_to_court'])
            ->has('documents', '>=', 1)
            ->with(['complainants', 'respondents', 'hearings', 'documents:id,lupon_case_id,document_type']);

        $hasOlderCases = (clone $casesDataQuery)->where(function ($q) use ($cutoffDate) {
            $q->where('settled_at', '<', $cutoffDate)
                ->orWhere(function ($sub) use ($cutoffDate) {
                    $sub->whereNull('settled_at')->where('updated_at', '<', $cutoffDate);
                });
        })->exists();

        $casesData = $casesDataQuery->get()
            ->filter(function ($case) {
                return $case->docs_completeness === 100;
            })
            ->filter(function ($case) use ($cutoffDate) {
                $date = $case->settled_at ?? $case->updated_at;

                return $date >= $cutoffDate;
            })
            ->sortByDesc(fn ($c) => $c->settled_at ?? $c->updated_at);

        // 3. Build a unified temporal structure: Year > Month > [reports, cases]
        $archive = [];

        foreach ($finalizedReports as $report) {
            $archive[$report->year][$report->month]['reports'][] = $report;
        }

        foreach ($casesData as $case) {
            $date = $case->settled_at ?? $case->updated_at;
            $year = $date->year;
            $month = $date->month;
            $archive[$year][$month]['cases'][] = $case;
        }

        foreach ($archive as $year => &$months) {
            krsort($months);
        }
        krsort($archive);

        // No KP Form template discovery in V2
        $templates = collect();

        return view('folderized-reports.index', compact('archive', 'templates', 'hasOlderCases'));
    }

    /**
     * API Endpoint: Fetch cases older than 24 months.
     */
    public function fetchOlder()
    {
        $cutoffDate = now()->subMonths(24);

        $casesData = \App\Models\LuponCase::whereIn('status', ['settled', 'certified_to_court'])
            ->has('documents', '>=', 1)
            ->with(['complainants', 'respondents', 'hearings', 'documents:id,lupon_case_id,document_type'])
            ->get()
            ->filter(function ($case) {
                return $case->docs_completeness === 100;
            })
            ->filter(function ($case) use ($cutoffDate) {
                $date = $case->settled_at ?? $case->updated_at;

                return $date < $cutoffDate;
            })
            ->sortByDesc(fn ($c) => $c->settled_at ?? $c->updated_at);

        $archive = [];
        foreach ($casesData as $case) {
            $date = $case->settled_at ?? $case->updated_at;
            $year = $date->year;
            $month = $date->month;
            $archive[$year][$month]['cases'][] = $case;
        }

        foreach ($archive as $year => &$months) {
            krsort($months);
        }
        krsort($archive);

        return response()->json($archive);
    }
}
