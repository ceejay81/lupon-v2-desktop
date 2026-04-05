@extends('layouts.app')

@section('title', 'Reports | Lupon')
@section('page-title', 'Reports Overview')

@section('content')
    <div style="padding: 1.5rem;">
        
        <!-- Metrics Row -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="ph ph-folder-open"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total Cases</h3>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $totalCases }}</p>
                </div>
            </div>

            <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="ph ph-check-circle"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Settled</h3>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $settledCases }}</p>
                </div>
            </div>

            <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="ph ph-spinner"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Ongoing</h3>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $ongoingCases }}</p>
                </div>
            </div>

            <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="ph ph-chart-pie"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Success Rate</h3>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $settlementRate }}%</p>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            
            <!-- Report Generation Card -->
            <div class="card" style="padding: 2rem; border-top: 4px solid var(--primary-color);">
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Generate & Archive Monthly Package</h3>
                    <p style="color: var(--text-muted);">Select a period to generate your monthly transmittal report and endorsement letter. You can archive them into your digital filing cabinet once finalized.</p>
                </div>
                
                <form action="{{ route('reports.finalize') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: end; background: var(--bg-secondary); padding: 1.5rem; border-radius: 12px;">
                        <div class="form-group">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Select Month</label>
                            <select name="month" id="report_month" class="form-control" style="width: 100%; border-radius: 8px;">
                                @for ($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Select Year</label>
                            <select name="year" id="report_year" class="form-control" style="width: 100%; border-radius: 8px;">
                                @for ($y=$currentYear; $y>=$currentYear-5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary" style="padding: 0 1.5rem; height: 42px; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);">
                                <i class="ph ph-archive-box"></i>
                                Finalize & Archive Month
                            </button>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                        <button type="button" 
                                onclick="generatePreview('kp-form-28')" 
                                class="btn" 
                                style="background: var(--bg-card); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; gap: 0.5rem; height: 48px; border-radius: 12px; font-weight: 500; transition: all 0.2s;">
                            <i class="ph ph-eye" style="color: var(--primary-color);"></i>
                            Preview Transmittal Report
                        </button>
                        
                        <button type="button" 
                                onclick="generatePreview('endorsement')" 
                                class="btn" 
                                style="background: var(--bg-card); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; gap: 0.5rem; height: 48px; border-radius: 12px; font-weight: 500; transition: all 0.2s;">
                            <i class="ph ph-eye" style="color: var(--primary-color);"></i>
                            Preview Endorsement Letter
                        </button>
                    </div>
                </form>

                <div style="margin-top: 1.5rem; padding: 1.5rem; border-radius: 12px; background: var(--bg-page); border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="ph ph-info" style="color: var(--accent-blue); font-size: 1.5rem;"></i>
                        <strong style="color: var(--text-primary);">Archiving Workflow (Follow Carefully)</strong>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div style="padding: 1rem; background: var(--bg-hover); border-radius: 8px;">
                            <p style="font-size: 0.875rem; color: var(--text-primary); margin: 0; line-height: 1.5;">
                                <strong>Step 1: Preview & Save</strong><br>
                                Click "Preview" for both reports. In the editor, click the <strong>💾 Save Document</strong> button. This creates the draft content.
                            </p>
                        </div>
                        <div style="padding: 1rem; background: var(--bg-hover); border-radius: 8px;">
                            <p style="font-size: 0.875rem; color: var(--text-primary); margin: 0; line-height: 1.5;">
                                <strong>Step 2: Finalize & Archive</strong><br>
                                Once your previews are saved, click <strong>Finalize & Archive Month</strong> to permanently store the files in your filing cabinet.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporting Activity -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700;">Reporting Activity & History</h3>
                    <a href="{{ route('folderized-reports.index') }}" style="font-size: 0.875rem; color: var(--primary-color); text-decoration: none; font-weight: 600;">View Filing Cabinet →</a>
                </div>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 1rem 0; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Period</th>
                                <th style="padding: 1rem 0; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Document Type</th>
                                <th style="padding: 1rem 0; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Status</th>
                                <th style="padding: 1rem 0; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Last Modified</th>
                                <th style="padding: 1rem 0; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReports as $report)
                                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;">
                                    <td style="padding: 1rem 0;">
                                        <span style="font-weight: 600; color: var(--text-primary);">{{ \Carbon\Carbon::create($report->year, $report->month, 1)->format('M Y') }}</span>
                                    </td>
                                    <td style="padding: 1rem 0;">
                                        <span style="font-size: 0.875rem; color: var(--text-muted);">{{ $report->type }}</span>
                                    </td>
                                    <td style="padding: 1rem 0;">
                                        @if($report->status === 'finalized')
                                            <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 0.75rem; font-weight: 600;">
                                                <i class="ph-fill ph-check-circle"></i> Archived
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: rgba(245, 158, 11, 0.1); color: #d97706; font-size: 0.75rem; font-weight: 600;">
                                                <i class="ph-fill ph-pencil-circle"></i> Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem 0; font-size: 0.875rem; color: var(--text-muted);">
                                        {{ $report->updated_at->diffForHumans() }}
                                    </td>
                                    <td style="padding: 1rem 0; text-align: right;">
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                            @if($report->status === 'finalized')
                                                <!-- Archived Action: View Snapshot -->
                                                <a href="{{ route('reports.show', $report->type) }}?month={{ $report->month }}&year={{ $report->year }}" 
                                                   title="View Interactive Snapshot" 
                                                   style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; color: var(--primary-color); background: rgba(59, 130, 246, 0.1); transition: all 0.2s;">
                                                    <i class="ph ph-eye"></i>
                                                </a>
                                                
                                                <!-- Print Official Report Action -->
                                                <a href="{{ route('reports.print', $report->id) }}?print=1" 
                                                   title="Print Official Report (High Fidelity)" 
                                                   style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; color: #10b981; background: rgba(16, 185, 129, 0.1); transition: all 0.2s;">
                                                    <i class="ph ph-printer"></i>
                                                </a>
                                            @else
                                                <!-- Draft Action: Edit Draft -->
                                                <a href="{{ route('reports.show', $report->type) }}?month={{ $report->month }}&year={{ $report->year }}" 
                                                   title="Edit/Manage Draft" 
                                                   style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; color: #f59e0b; background: rgba(245, 158, 11, 0.1); transition: all 0.2s;">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 3rem 0; text-align: center; color: var(--text-muted); font-style: italic;">
                                        No recent reporting activity recorded.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function generatePreview(type) {
            const month = document.getElementById('report_month').value;
            const year = document.getElementById('report_year').value;
            window.open(`{{ url('reports') }}/${type}?month=${month}&year=${year}`, '_blank');
        }
    </script>
@endsection

