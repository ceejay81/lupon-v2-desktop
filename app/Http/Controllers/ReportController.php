<?php

namespace App\Http\Controllers;

use App\Models\Report;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Fetch some basic stats for the reports dashboard
        $totalCases = \App\Models\LuponCase::count();
        $settledCases = \App\Models\LuponCase::where('status', 'settled')->count();
        $ongoingCases = \App\Models\LuponCase::whereIn('status', ['under_mediation', 'under_conciliation', 'under_arbitration'])->count();

        $cfaCases = \App\Models\LuponCase::where('status', 'certified_to_court')->count();

        return view('reports.index', compact(
            'totalCases', 'settledCases', 'ongoingCases', 'cfaCases', 'currentYear', 'currentMonth'
        ));
    }

    private function getReportRecord(int $month, int $year, string $type)
    {
        return Report::where('month', $month)
            ->where('year', $year)
            ->where('type', $type)
            ->first();
    }

    private function wrapWithBranding($content, $isPdf = false, $month = null, $year = null, $caseId = null)
    {
        $cssPath = public_path('documents/css/editor-style.css');
        $headerPath = public_path('documents/images/header.png');
        $watermarkPath = public_path('documents/images/watermark.png');

        // Load professional CSS specifically designed for these reports
        $css = file_exists($cssPath) ? file_get_contents($cssPath) : '';

        // Base64 encode images to ensure snapshots are self-contained and PDF compatible
        $headerBase64 = '';
        if (file_exists($headerPath)) {
            $type = pathinfo($headerPath, PATHINFO_EXTENSION);
            $data = file_get_contents($headerPath);
            $headerBase64 = 'data:image/'.$type.';base64,'.base64_encode($data);
        }

        $watermarkBase64 = '';
        if (file_exists($watermarkPath)) {
            $type = pathinfo($watermarkPath, PATHINFO_EXTENSION);
            $data = file_get_contents($watermarkPath);
            $watermarkBase64 = 'data:image/'.$type.';base64,'.base64_encode($data);
        }

        // Return the robustly rendered Blade view as a standalone HTML string
        return view('documents.layout', [
            'savedContent' => $content,
            'css' => $css,
            'header' => $headerBase64,
            'watermark' => $watermarkBase64,
            'isArchive' => true,
            'isPdf' => $isPdf,
            'month' => $month,
            'year' => $year,
            'case_id' => $caseId,
        ])->render();
    }

    public function printReport(Report $report)
    {
        // Wrap with branding for high-fidelity browser printing
        return $this->wrapWithBranding($report->content, true, $report->month, $report->year);
    }

    public function saveContent(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
            'type' => 'required|string',
            'content' => 'required|string',
        ]);

        /** @var Report|null $report */
        $report = Report::where('month', $request->month)
            ->where('year', $request->year)
            ->where('type', $request->type)
            ->first();

        if (! $report) {
            $report = new Report;
            $report->month = (int) $request->month;
            $report->year = (int) $request->year;
            $report->type = $request->type;
        }

        $report->status = 'draft';
        $report->setAttribute('content', $request->input('content'));
        $report->save();

        return response()->json(['success' => true]);
    }

    /**
     * Finalize the report package for the month.
     */
    public function finalize(\Illuminate\Http\Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Find all draft reports for this period
        $reports = Report::where('month', $month)->where('year', $year)->get();

        if ($reports->isEmpty()) {
            return back()->with('error', 'No report content found for this month. You must first click "Preview" on a report and then click "Save Document" to create the content for archiving.');
        }

        $folderName = sprintf('%04d-%02d', $year, $month);
        $directory = "reports/{$year}/{$folderName}";

        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);

        foreach ($reports as $report_item) {
            /** @var \App\Models\Report $report_item */

            // Just mark as finalized and keep the raw content
            $report_item->status = 'finalized';
            $report_item->file_path = 'digital_archive'; // Extension-free marker
            $report_item->submitted_at = \Illuminate\Support\Carbon::now();
            $report_item->submitted_by = auth()->id() ?? 1;
            $report_item->save();
        }

        return redirect()->route('folderized-reports.index')->with('success', "Report package for {$folderName} finalized successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show($type, \Illuminate\Http\Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);
        $caseId = $request->query('case_id');
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        // Specific Case Context (New!)
        $case = $caseId ? \App\Models\LuponCase::with(['complainants', 'respondents', 'pangkats'])->find($caseId) : null;

        // Create a period date for easy formatting in views
        $period = \Carbon\Carbon::create($year, $month, 1);

        // Check for saved content
        $reportRecord = $this->getReportRecord((int) $month, (int) $year, $type);
        $savedContent = $reportRecord ? $reportRecord->content : null;

        // Unified Type Mapping: Handle both internal keys and human-friendly slugs from the cabinet
        $slug = \Illuminate\Support\Str::slug($type);

        if ($type === 'endorsement' || $slug === 'endorsement-letter') {
            return view('documents.endorsement', compact('settings', 'month', 'year', 'savedContent', 'period', 'case'));
        }

        if ($type === 'monthly-transmittal' || $type === 'kp-form-28' || $slug === 'monthly-transmittal-report') {
            $cases = \App\Models\LuponCase::with(['complainants', 'respondents'])
                ->whereMonth('filed_date', $month)
                ->whereYear('filed_date', $year)
                ->get();

            return view('documents.monthly-transmittal-report', compact('cases', 'settings', 'month', 'year', 'savedContent', 'period', 'case'));
        }

        if ($type === 'cfa-cases') {
            $cases = \App\Models\LuponCase::with(['complainants', 'respondents'])
                ->where('status', 'certified_to_court')
                ->whereMonth('filed_date', $month)
                ->whereYear('filed_date', $year)
                ->get();

            return view('documents.cfa-cases-report', compact('cases', 'settings', 'month', 'year', 'savedContent', 'period', 'case'));
        }

        // Dynamic fallback for all other KP templates (kp-form-16, certificate-to-file-action, etc.)
        if (view()->exists("documents.{$type}")) {
            $cases = collect();

            return view("documents.{$type}", compact('settings', 'month', 'year', 'savedContent', 'period', 'cases', 'case'));
        }

        abort(404, "Report template [{$type}] not found in resources/views/documents/");
    }

    public function printTemplate($key)
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $month = now()->month;
        $year = now()->year;
        $period = \Carbon\Carbon::create($year, $month, 1);
        $savedContent = null;
        $cases = collect();

        $viewPath = "documents.{$key}";
        if (! view()->exists($viewPath)) {
            abort(404, "Template {$key} not found");
        }

        $html = view($viewPath, compact('settings', 'month', 'year', 'period', 'savedContent', 'cases'))->render();

        // Use branding wrapper for templates too
        return $this->wrapWithBranding($html, true, $month, $year);
    }
}
