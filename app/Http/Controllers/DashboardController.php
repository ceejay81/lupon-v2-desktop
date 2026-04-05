<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCases = \App\Models\LuponCase::count();
        $settledCases = \App\Models\LuponCase::where('status', 'settled')->count();
        $ongoingCases = \App\Models\LuponCase::whereIn('status', ['under_mediation', 'under_conciliation', 'under_arbitration'])->count();

        $casesThisMonth = \App\Models\LuponCase::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $recentCases = \App\Models\LuponCase::with('documents')
            ->where('created_at', '>=', now()->subWeek())
            ->latest()
            ->take(5)
            ->get();
        $upcomingHearings = \App\Models\Hearing::whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->with('luponCase')
            ->get();

        // MOV 1: Searchable Database (Total cases)
        $mov1Count = $totalCases;

        // MOV 2: Folderized Reports (Cases with at least one document)
        $mov2Count = \App\Models\LuponCase::whereHas('documents')->count();
        $mov2Total = $totalCases;
        $mov2Percent = $mov2Total > 0 ? round(($mov2Count / $mov2Total) * 100) : 0;

        // Attendance Stats
        $attendanceCount = \App\Models\Hearing::whereHas('attendances')->count();
        $attendanceTotal = \App\Models\Hearing::count();
        $attendancePercent = $attendanceTotal > 0 ? round(($attendanceCount / $attendanceTotal) * 100) : 0;

        // Form 1 Status (Dummy date for Apr 15 as per wireframe)
        $form1DueDate = now()->month >= 4 && now()->day <= 15
            ? now()->setDay(15)->setMonth(4)
            : now()->addMonth()->setDay(15);
        $daysToForm1 = now()->diffInDays($form1DueDate, false);

        // Urgent Alerts
        $urgentAlerts = [
            'missing_docs' => $totalCases - $mov2Count,
            'form1_days' => ceil($daysToForm1),
        ];

        return view('dashboard.index', compact(
            'totalCases', 'settledCases', 'ongoingCases', 'recentCases', 'upcomingHearings',
            'casesThisMonth', 'mov1Count', 'mov2Count', 'mov2Total', 'mov2Percent',
            'attendanceCount', 'attendanceTotal', 'attendancePercent',
            'daysToForm1', 'urgentAlerts'
        ));
    }
}
