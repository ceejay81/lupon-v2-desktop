<?php

namespace App\Http\Controllers;

class FolderizedReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Fetch finalized reports
        $finalizedReports = \App\Models\Report::where('status', 'finalized')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // 2. Fetch settled and certified cases with 100% compliance
        $casesData = \App\Models\LuponCase::whereIn('status', ['settled', 'certified_to_court'])
            ->with(['complainantCitizen', 'respondentCitizen', 'hearings', 'documents'])
            ->get()
            ->filter(function ($case) {
                // Only archive cases that are 100% compliant (Process + Result docs exist)
                return $case->docs_completeness === 100;
            })
            ->sortByDesc(fn ($c) => $c->settled_at ?? $c->updated_at);

        // 3. Build a unified temporal structure: Year > Month > [reports, cases]
        $archive = [];

        // Add reports to archive
        foreach ($finalizedReports as $report) {
            $archive[$report->year][$report->month]['reports'][] = $report;
        }

        // Add cases to archive
        foreach ($casesData as $case) {
            $date = $case->settled_at ?? $case->updated_at;
            $year = $date->year;
            $month = $date->month;
            $archive[$year][$month]['cases'][] = $case;
        }

        // Sort months descending for each year
        foreach ($archive as $year => &$months) {
            krsort($months);
        }
        krsort($archive);

        // 4. Discover official KP Form templates
        $templatesPath = resource_path('views/documents');
        $templateFiles = glob($templatesPath.'/*.blade.php');
        $templates = collect($templateFiles)->map(function ($path) {
            $filename = basename($path, '.blade.php');
            if ($filename === 'layout') {
                return null;
            }

            $titles = [
                'amicable-settlement' => 'Amicable Settlement (KP Form 16)',
                'certificate-to-file-action' => 'Certification to File Action (KP Form 20)',
                'endorsement' => 'Endorsement Letter',
                'invitation-notice' => 'Invitation Notice',
                'kasabutan' => 'Kasabutan (Settlement)',
                'monthly-transmittal-report' => 'Monthly Transmittal Report',
                'notice-of-hearing' => 'Notice of Hearing (KP Form 8)',
                'status-of-case' => 'Status of Case (Record)',
                'summon' => 'Summons (KP Form 9)',
            ];

            return [
                'key' => $filename,
                'title' => $titles[$filename] ?? ucwords(str_replace('-', ' ', $filename)),
                'icon' => str_contains($filename, 'report') ? 'ph-chart-bar' : 'ph-file-text',
            ];
        })->filter()->values();

        return view('folderized-reports.index', compact('archive', 'templates'));
    }
}
