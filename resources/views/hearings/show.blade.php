@extends('layouts.app')

@section('title', 'Hearing Details | Lupon')
@section('page-title', 'Hearings — Detail')

@section('content')
<div style="padding: 2rem 32px;">

    <!-- Breadcrumbs & Status Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.75rem;">
                <a href="{{ route('hearings.index') }}" style="color: var(--text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--accent-blue)'" onmouseout="this.style.color='var(--text-muted)'">Hearings</a>
                <i class="ph ph-caret-right" style="font-size: 0.75rem;"></i>
                <span style="color: var(--text-primary);">Detail View</span>
            </nav>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">
                    {{ ucfirst($hearing->hearing_type) }} Hearing
                </h1>
                <div style="display: flex; gap: 0.5rem;">
                    @php
                        $statusColor = match($hearing->status) {
                            'completed' => 'var(--success)',
                            'cancelled', 'failed' => 'var(--danger)',
                            default => 'var(--accent-blue)',
                        };
                        $statusBg = match($hearing->status) {
                            'completed' => 'var(--success-light)',
                            'cancelled', 'failed' => 'var(--danger-light)',
                            default => 'var(--accent-light)',
                        };
                    @endphp
                    <span style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 6px 14px; border-radius: 99px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid {{ $statusColor }}20;">
                        {{ $hearing->status }}
                    </span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="Livewire.dispatch('editHearing', { hearingId: {{ $hearing->id }} })" class="btn-secondary" style="display: flex; align-items: center; gap: 0.625rem; height: 42px;">
                <i class="ph ph-pencil"></i> Edit Details
            </button>
            <a href="{{ route('hearings.index') }}" class="btn-secondary" style="display: flex; align-items: center; gap: 0.625rem; height: 42px; text-decoration: none;">
                <i class="ph ph-arrow-left"></i> Back to Schedule
            </a>
        </div>
    </div>




    {{-- HearingForm modal (for editing) --}}
    @livewire('hearing-form', [], 'hearing-form-show')

    <div class="dashboard-grid" style="padding: 0; gap: 2rem;">
        
        <!-- MAIN CONTENT (Left) -->
        <div style="grid-column: span 8; display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Schedule & Logistics Card -->
            <div class="card" style="padding: 0;">
                <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-hover);">
                    <h3 style="font-size: 0.875rem; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="ph-bold ph-calendar-check" style="color: var(--accent-blue); font-size: 1.25rem;"></i>
                        Logistics & Schedule
                    </h3>
                </div>
                <div style="padding: 2.5rem; display: grid; grid-template-columns: repeat(2, 1fr); gap: 3rem;">
                    <div>
                        <label style="display: block; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">Scheduled Date & Time</label>
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="width: 52px; height: 52px; background: var(--accent-light); color: var(--accent-blue); border-radius: 14px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <span style="font-size: 1.25rem; font-weight: 800; line-height: 1;">{{ $hearing->scheduled_at->format('d') }}</span>
                                <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase;">{{ $hearing->scheduled_at->format('M') }}</span>
                            </div>
                            <div>
                                <p style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary);">{{ $hearing->scheduled_at->format('F d, Y') }}</p>
                                <p style="font-size: 0.875rem; color: var(--text-secondary); font-weight: 500;">{{ $hearing->scheduled_at->format('h:i A') }} (PH Local Time)</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">Meeting Location</label>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 52px; height: 52px; background: var(--warning-light, rgba(245, 158, 11, 0.1)); color: var(--warning, #f59e0b); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="ph-fill ph-map-pin"></i>
                            </div>
                            <div>
                                <p style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary);">{{ $hearing->location }}</p>
                                <p style="font-size: 0.875rem; color: var(--text-secondary); font-weight: 500;">Official Barangay Session Hall</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance & Outcome Card -->
            <div class="card" style="padding: 0; border-top: 4px solid var(--success);">
                <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-hover); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 0.875rem; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="ph-bold ph-users-three" style="color: var(--success); font-size: 1.25rem;"></i>
                        Attendance & Outcome Record
                    </h3>
                </div>
                <div style="padding: 2rem;">
                    @livewire('hearing-attendance', ['hearing' => $hearing])
                </div>
            </div>

        </div>

        <!-- SIDEBAR (Right) -->
        <div style="grid-column: span 4; display: flex; flex-direction: column; gap: 2rem;">

            <!-- Minutes & Notes (Moved) -->
            @if($hearing->minutes || $hearing->outcome)
                <div class="card" style="padding: 0; background-color: var(--bg-hover); border: 1px solid var(--border-color);">
                    <div style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); background: var(--bg-card);">
                        <h3 style="font-size: 0.8125rem; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.625rem;">
                            <i class="ph ph-notebook-fill" style="color: var(--accent-blue); font-size: 1.125rem;"></i>
                            Resolution Record
                        </h3>
                    </div>
                    <div style="padding: 1.75rem;">
                        @if($hearing->outcome)
                            <div style="margin-bottom: 1.75rem;">
                                <label style="display: block; font-size: 0.625rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem; padding-left: 2px;">Hearing Outcome</label>
                                <div style="display: flex; align-items: flex-start; gap: 0.875rem; background: var(--bg-card); padding: 1rem; border-radius: 12px; border: 1px solid var(--success)20; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.03);">
                                    <div style="width: 24px; height: 24px; background: var(--success-light); color: var(--success); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; flex-shrink: 0;">
                                        <i class="ph ph-check-square"></i>
                                    </div>
                                    <p style="font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.4;">{{ $hearing->outcome }}</p>
                                </div>
                            </div>
                        @endif

                        @if($hearing->minutes)
                            <div>
                                <label style="display: block; font-size: 0.625rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem; padding-left: 2px;">Session Minutes</label>
                                <div style="background: var(--bg-page); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); font-size: 0.8125rem; line-height: 1.6; color: var(--text-secondary); white-space: pre-wrap; font-style: italic;">{{ $hearing->minutes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Case Brief Sidebar Card -->
            <div class="card" style="padding: 0; background: var(--bg-card);">
                <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-hover);">
                    <h3 style="font-size: 0.875rem; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="ph-bold ph-scales" style="color: var(--accent-blue); font-size: 1.25rem;"></i>
                        Case Brief
                    </h3>
                </div>
                <div style="padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem;">
                    <div>
                        <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Official Case ID</p>
                        <a href="{{ route('cases.show', $hearing->luponCase) }}" style="font-size: 1.25rem; font-weight: 800; color: var(--accent-blue); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                            {{ $hearing->luponCase->case_number }}
                            <i class="ph ph-arrow-square-out" style="font-size: 1rem;"></i>
                        </a>
                    </div>
                    
                    <div>
                        <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Nature of Dispute</p>
                        <p style="font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); line-height: 1.4;">{{ $hearing->luponCase->nature_of_case }}</p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1.25rem; background: var(--bg-hover); border-radius: 12px; border: 1px solid var(--border-color);">
                        <div>
                            <p style="font-size: 0.625rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Case Status</p>
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--accent-blue);">{{ $hearing->luponCase->status_label }}</span>
                        </div>
                        <div>
                            <p style="font-size: 0.625rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Filed Date</p>
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-primary);">{{ $hearing->luponCase->filed_date->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <hr style="border: none; border-top: 1px solid var(--border-color);">

                    <div>
                        <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Principal Parties</p>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--accent-light); color: var(--accent-blue); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">C</div>
                                <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">{{ $hearing->luponCase->complainant }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">R</div>
                                <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">{{ $hearing->luponCase->respondent }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card" style="padding: 1.5rem; background: var(--bg-hover); border: 1px solid var(--border-color);">
                <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph-bold ph-lightning" style="color: var(--warning);"></i> Quick Actions
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="{{ route('cases.show', $hearing->luponCase) }}" class="btn-secondary" style="display: flex; align-items: center; gap: 0.75rem; height: 42px; text-decoration: none; font-size: 0.8125rem; justify-content: center;">
                        <i class="ph ph-folder-open"></i> Open Case Folder
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

