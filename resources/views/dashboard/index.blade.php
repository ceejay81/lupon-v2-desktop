@extends('layouts.app')

@section('title', 'Dashboard | Lupon')
@section('page-title', '🏛️ LUPON Dashboard')

@section('content')
<div style="padding: var(--spacing-6);">
    <div class="dashboard-grid">
        
        <!-- API: KPI Metrics Row (4 columns) -->
        <div class="kpi-row">
            <!-- Cases This Month -->
            <div class="kpi-card" style="border-top: 3px solid var(--accent-blue);">
                <div class="kpi-header">
                    <span class="kpi-title">📁 Cases This Month</span>
                    <i class="ph ph-folder-open" style="color: var(--accent-blue); font-size: 1.25rem;"></i>
                </div>
                <div class="kpi-value">{{ $casesThisMonth }}</div>
                <div class="kpi-meta" style="color: var(--success);">
                    <i class="ph ph-trend-up"></i> ↑ 8% vs last month
                </div>
            </div>

            <!-- Settlement Rate -->
            <div class="kpi-card" style="border-top: 3px solid var(--success);">
                <div class="kpi-header">
                    <span class="kpi-title">⚖️ Settlement Rate</span>
                    <i class="ph ph-scales" style="color: var(--success); font-size: 1.25rem;"></i>
                </div>
                <div class="kpi-value">
                    {{ $totalCases > 0 ? round(($settledCases / $totalCases) * 100) : 0 }}%
                </div>
                <div class="kpi-meta" style="color: var(--success);">
                    <i class="ph ph-trend-up"></i> ↑ 5% efficiency
                </div>
            </div>

            <!-- Form 1 Status -->
            <div class="kpi-card" style="border-top: 3px solid var(--warning);">
                <div class="kpi-header">
                    <span class="kpi-title">📄 Form 1 Status</span>
                    <i class="ph ph-file-text" style="color: var(--warning); font-size: 1.25rem;"></i>
                </div>
                <div class="kpi-value-sm" style="margin-bottom: 0;">{{ $urgentAlerts['form1_days'] }} days left</div>
                <div class="kpi-meta">Due by {{ now()->addMonth()->setDay(15)->format('M d, Y') }}</div>
            </div>

            <!-- Database (MOV 1) -->
            <div class="kpi-card" style="border-top: 3px solid var(--info);">
                <div class="kpi-header">
                    <span class="kpi-title">🔍 Database (MOV 1)</span>
                    <i class="ph ph-database" style="color: var(--info); font-size: 1.25rem;"></i>
                </div>
                <div class="kpi-value">{{ $totalCases }}</div>
                <div class="kpi-meta" style="color: var(--success);">
                    <i class="ph ph-check-circle"></i> ✓ Ready for Assessment
                </div>
            </div>
        </div>

        <!-- 40/60 Layout -->
        <div class="two-column-layout">
            
            <!-- Left: MOV Compliance (40%) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📋 MOV COMPLIANCE</h3>
                    <span class="badge badge-info">Level 1 Ready</span>
                </div>
                <div class="card-body">
                    <!-- MOV 1 -->
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; align-items: center;">
                            <span style="font-weight: 600; font-size: 0.85rem;">✓ MOV 1: Searchable Database</span>
                            <span style="font-size: 0.75rem; color: var(--success); font-weight: 700;">100%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar progress-success" style="width: 100%"></div>
                        </div>
                        <ul style="list-style: none; padding: 0; font-size: 0.75rem; color: var(--text-secondary);">
                            <li>• {{ $totalCases }} cases in database</li>
                            <li>• Search function active</li>
                        </ul>
                    </div>

                    <!-- MOV 2 -->
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; align-items: center;">
                            <span style="font-weight: 600; font-size: 0.85rem;">◐ MOV 2: Folderized Reports</span>
                            <span style="font-size: 0.75rem; color: var(--info); font-weight: 700;">{{ $mov2Percent }}%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar progress-info" style="width: {{ $mov2Percent }}%"></div>
                        </div>
                        <ul style="list-style: none; padding: 0; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                            <li>• {{ $mov2Count }}/{{ $mov2Total }} cases complete</li>
                            <li>• {{ $mov2Total - $mov2Count }} need attention</li>
                        </ul>
                        <a href="{{ route('folderized-reports.index') }}" class="kpi-link">Fix Incomplete →</a>
                    </div>

                    <!-- Attendance Sheets -->
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; align-items: center;">
                            <span style="font-weight: 600; font-size: 0.85rem;">⚠ Attendance Sheets</span>
                            <span style="font-size: 0.75rem; color: var(--warning); font-weight: 700;">{{ $attendancePercent }}%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar progress-warning" style="width: {{ $attendancePercent }}%"></div>
                        </div>
                        <ul style="list-style: none; padding: 0; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                            <li>• {{ $attendanceCount }}/{{ $attendanceTotal }} hearings have sheets</li>
                        </ul>
                        <a href="{{ route('hearings.index') }}" class="kpi-link">Upload Missing →</a>
                    </div>

                    <div style="margin-top: 2rem; border-top: 1px solid var(--gray-200); padding-top: 1rem; text-align: center;">
                        <a href="{{ route('reports.index') }}" class="btn-link">Full Compliance Report →</a>
                    </div>
                </div>
            </div>

            <!-- Right: Alerts & Upcoming (60%) -->
            <div style="display: flex; flex-direction: column; gap: var(--spacing-6);">
                
                <!-- Alerts Card -->
                <div class="card card-accent" style="border-top-color: var(--danger);">
                    <div class="card-header">
                        <h3 class="card-title" style="color: var(--danger);">🔴 URGENT ALERTS</h3>
                    </div>
                    <div class="card-body" style="padding: 1.25rem;">
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                            @if($urgentAlerts['missing_docs'] > 0)
                            <li style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.875rem;">
                                <i class="ph ph-warning-octagon" style="color: var(--danger); font-size: 1.1rem; margin-top: 2px;"></i>
                                <span><strong>{{ $urgentAlerts['missing_docs'] }} cases</strong> missing MOV 2 documents</span>
                            </li>
                            @endif
                            <li style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.875rem;">
                                <i class="ph ph-calendar-x" style="color: var(--danger); font-size: 1.1rem; margin-top: 2px;"></i>
                                <span>Form 1 monthly report due in <strong>{{ $urgentAlerts['form1_days'] }} days</strong></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Hearings Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📅 UPCOMING HEARINGS</h3>
                        <div class="card-actions">
                            <a href="{{ route('hearings.index') }}" class="btn-sm">View Calendar</a>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        @forelse($upcomingHearings as $hearing)
                        @if($hearing->luponCase)
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; gap: 1rem;">
                            <div style="background: var(--accent-light); color: var(--accent-blue); padding: 0.5rem; border-radius: var(--radius-md); text-align: center; min-width: 80px;">
                                <div style="font-size: 0.7rem; font-weight: 700;">{{ $hearing->scheduled_at->format('h:i A') }}</div>
                                <div style="font-size: 0.6rem; text-transform: uppercase; margin-top: 2px;">{{ $hearing->hearing_type }}</div>
                                <div style="font-size: 0.6rem; color: var(--text-muted); margin-top: 1px;">{{ $hearing->scheduled_at->format('M d') }}</div>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 700; font-size: 0.875rem;">{{ $hearing->luponCase->case_number }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ Str::limit($hearing->luponCase->complainant, 20) }} vs. {{ Str::limit($hearing->luponCase->respondent, 20) }}
                                </div>
                            </div>
                            <a href="{{ route('hearings.show', $hearing) }}" class="btn-icon"><i class="ph ph-caret-right"></i></a>
                        </div>
                        @endif
                        @empty
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                            No upcoming hearings scheduled.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        <!-- Full Width: Recent Cases -->
        <div class="full-width-table">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📋 RECENT CASES</h3>
                    <div class="card-actions">
                        <a href="{{ route('cases.index') }}" class="btn-sm">View All Cases →</a>
                    </div>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Parties</th>
                                <th>Nature</th>
                                <th>Status</th>
                                <th>Filed</th>
                                <th>Docs</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCases as $case)
                            <tr>
                                <td class="case-number">{{ $case->case_number }}</td>
                                <td>
                                    <strong>{{ Str::limit($case->complainant, 20) }}</strong>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">vs {{ Str::limit($case->respondent, 20) }}</div>
                                </td>
                                <td>{{ Str::limit($case->nature_of_case, 25) }}</td>
                                <td>
                                    @php
                                        $activeStatuses = ['under_mediation', 'under_conciliation', 'under_arbitration'];
                                    @endphp
                                    @if($case->status === 'settled')
                                        <span class="badge badge-success">● Settled</span>
                                    @elseif(in_array($case->status, $activeStatuses))
                                        <span class="badge badge-info">● Active</span>
                                    @elseif($case->status === 'certified_to_court')
                                        <span class="badge badge-danger">● To Court</span>
                                    @else
                                        <span class="badge badge-warning">🟡 {{ ucfirst(str_replace('_', ' ', $case->status)) }}</span>
                                    @endif
                                </td>
                                <td>{{ $case->created_at->format('m/d/y') }}</td>
                                <td>
                                    @php $percent = $case->docs_completeness; @endphp
                                    @if($percent === 100)
                                        <div style="display: flex; align-items: center; gap: 0.4rem; color: #10b981; font-weight: 700; font-size: 0.85rem;">
                                            <i class="ph ph-check-square" style="font-size: 1.1rem;"></i> 100%
                                        </div>
                                    @else
                                        <div style="display: flex; align-items: center; gap: 0.4rem; color: #f59e0b; font-weight: 700; font-size: 0.85rem;">
                                            <i class="ph ph-warning" style="font-size: 1.1rem;"></i> {{ $percent }}%
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cases.show', $case) }}" class="btn-link">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                    <i class="ph ph-folder-open" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                                    No cases filed in the last 7 days.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
