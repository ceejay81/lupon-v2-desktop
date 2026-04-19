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
                    <span class="kpi-title">📅 Cases This Month</span>
                    <i class="ph ph-calendar-plus" style="color: var(--accent-blue); font-size: 1.25rem;"></i>
                </div>
                <div class="kpi-value">{{ $casesThisMonth }}</div>
                <div class="kpi-meta" style="color: var(--text-muted);">
                    New filings
                </div>
            </div>

            <!-- Total Cases -->
            <div class="kpi-card" style="border-top: 3px solid var(--info);">
                <div class="kpi-header">
                    <span class="kpi-title">📁 Total Cases</span>
                    <i class="ph ph-folder-open" style="color: var(--info); font-size: 1.25rem;"></i>
                </div>
                <div class="kpi-value">{{ $totalCases }}</div>
                <div class="kpi-meta" style="color: var(--text-muted);">
                    All time records
                </div>
            </div>

            <!-- Total Cases Settled -->
            <div class="kpi-card" style="border-top: 3px solid var(--success);">
                <div class="kpi-header">
                    <span class="kpi-title">🤝 Cases Settled</span>
                    <i class="ph ph-handshake" style="color: var(--success); font-size: 1.25rem;"></i>
                </div>
                <div class="kpi-value">{{ $settledCases }}</div>
                <div class="kpi-meta" style="color: var(--success);">
                    <i class="ph ph-check-circle"></i> Successfully resolved
                </div>
            </div>

            <!-- CFA Issued -->
            <div class="kpi-card" style="border-top: 3px solid var(--danger);">
                <div class="kpi-header">
                    <span class="kpi-title">📜 CFA Issued</span>
                    <i class="ph ph-certificate" style="color: var(--danger); font-size: 1.25rem;"></i>
                </div>
                <div class="kpi-value">{{ $cfaCases }}</div>
                <div class="kpi-meta" style="color: var(--danger);">
                    Certified to court
                </div>
            </div>
        </div>

        <div class="two-column-layout">
            <!-- Left: Upcoming Hearings (4fr) -->
            <div class="card" style="animation: slideInLeft 0.5s ease-out;">
                <div class="card-header">
                    <h3 class="card-title">📅 UPCOMING HEARINGS</h3>
                    <div class="card-actions">
                        <a href="{{ route('hearings.index') }}" class="btn-sm">View Calendar</a>
                    </div>
                </div>
                <div class="card-body" style="padding: 0;">
                    @forelse($upcomingHearings as $hearing)
                    @if($hearing->luponCase)
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; gap: 1rem; transition: background 0.2s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                        <div style="background: var(--accent-light); color: var(--accent-blue); padding: 0.5rem; border-radius: var(--radius-md); text-align: center; min-width: 80px;">
                            <div style="font-size: 0.7rem; font-weight: 700;">{{ $hearing->scheduled_at->format('h:i A') }}</div>
                            <div style="font-size: 0.6rem; text-transform: uppercase; margin-top: 2px;">{{ $hearing->hearing_type }}</div>
                            <div style="font-size: 0.6rem; color: var(--text-muted); margin-top: 1px;">{{ $hearing->scheduled_at->format('M d') }}</div>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; font-size: 0.875rem;">{{ $hearing->luponCase->case_number }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ Str::limit($hearing->luponCase->complainants->pluck('name')->join(', ') ?: $hearing->luponCase->complainant, 20) }} vs. {{ Str::limit($hearing->luponCase->respondents->pluck('name')->join(', ') ?: $hearing->luponCase->respondent, 20) }}
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

            <!-- Right: CFA Cases Preview (8fr) -->
            <div class="card" style="animation: slideInRight 0.5s ease-out;">
                <div class="card-header">
                    <h3 class="card-title">📜 RECENT CFA ISSUANCES</h3>
                    <div class="card-actions">
                        <span class="badge badge-danger">Certified to Court</span>
                    </div>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="data-table" style="border: none;">
                        <thead>
                            <tr style="background: transparent;">
                                <th style="padding-left: 24px;">Case No.</th>
                                <th>Parties</th>
                                <th>Reason for CFA</th>
                                <th style="padding-right: 24px;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cfaPreview as $case)
                            <tr style="transition: background 0.2s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                                <td style="padding-left: 24px;">
                                    <a href="{{ route('cases.show', $case) }}" class="case-number">{{ $case->case_number }}</a>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.8rem;">{{ Str::limit($case->complainants->pluck('name')->join(', ') ?: $case->complainant, 20) }}</div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">vs {{ Str::limit($case->respondents->pluck('name')->join(', ') ?: $case->respondent, 20) }}</div>
                                </td>
                                <td>
                                    <span style="font-size: 0.75rem;">Failed Conciliation</span>
                                </td>
                                <td style="padding-right: 24px; font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $case->updated_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                                    No CFA cases recorded yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Full Width: Recent Cases -->
        <div class="full-width-table" style="animation: fadeInUp 0.6s ease-out;">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📋 RECENT BLOTTER FILINGS</h3>
                    <div class="card-actions">
                        <a href="{{ route('cases.index') }}" class="btn-sm">View All Records →</a>
                    </div>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">Case No.</th>
                                <th>Parties</th>
                                <th>Nature</th>
                                <th>Current Status</th>
                                <th>Filed</th>
                                <th style="padding-right: 24px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCases as $case)
                            <tr style="transition: background 0.2s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                                <td class="case-number" style="padding-left: 24px;">{{ $case->case_number }}</td>
                                <td>
                                    <strong>{{ Str::limit($case->complainants->pluck('name')->join(', ') ?: $case->complainant, 20) }}</strong>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">vs {{ Str::limit($case->respondents->pluck('name')->join(', ') ?: $case->respondent, 20) }}</div>
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
                                        <span class="badge badge-warning">● {{ ucfirst(str_replace('_', ' ', $case->status)) }}</span>
                                    @endif
                                </td>
                                <td>{{ $case->created_at->format('m/d/y') }}</td>
                                <td style="padding-right: 24px;">
                                    <a href="{{ route('cases.show', $case) }}" class="btn-link">View Record</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
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

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}

.kpi-card { animation: fadeInUp 0.4s ease-out forwards; }
.kpi-card:nth-child(2) { animation-delay: 0.1s; }
.kpi-card:nth-child(3) { animation-delay: 0.2s; }
.kpi-card:nth-child(4) { animation-delay: 0.3s; }
</style>

    </div>
</div>
@endsection
