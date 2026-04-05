@extends('layouts.app')

@use(Illuminate\Support\Str)

@section('title', 'Case ' . $case->case_number . ' | Lupon')
@section('page-title', 'Case Record Details')

@section('content')
<div class="case-show-container">
    
    {{-- Case Header --}}
    <div class="case-header-modern">
        <div class="case-header-left">
            <div class="case-icon-large">
                <i class="ph ph-scales"></i>
            </div>
            <div class="case-title-section">
                <div class="case-breadcrumb">
                    <a href="{{ route('cases.index') }}">Cases</a>
                    <i class="ph ph-caret-right"></i>
                    <span>{{ $case->case_number }}</span>
                </div>
                <h1 class="case-title">{{ $case->complainant }} vs {{ $case->respondent }}</h1>
                <div class="case-meta">
                    <span class="case-number">{{ $case->case_number }}</span>
                    <span class="case-separator">•</span>
                    <span class="case-filed">Filed {{ $case->filed_date->format('M d, Y') }}</span>
                    <span class="case-separator">•</span>
                    <span class="case-nature">{{ $case->nature_of_case }}</span>
                </div>
            </div>
        </div>
        
        <div class="case-header-right">
            <div class="case-status-section">
                <span class="status-badge {{ $case->status_badge_class }}">
                    {{ $case->status_label }}
                </span>
            </div>
            <div class="case-actions-header">
                <button onclick="Livewire.dispatch('openHearingForm', { caseId: {{ $case->id }} })" class="btn-action btn-primary">
                    <i class="ph ph-calendar-plus"></i>
                    Schedule Hearing
                </button>
                <a href="{{ route('cases.edit', $case) }}" class="btn-action btn-secondary">
                    <i class="ph ph-pencil"></i>
                    Edit Case
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="case-content-grid">
        
        {{-- Left Column - Main Content --}}
        <div class="case-main-column">
            
            {{-- Overdue Banner --}}
            @if($slaData['state'] === 'overdue')
                <div x-data="{ show: true }" x-show="show" class="sla-overdue-banner alert alert-danger" role="alert">
                    <div class="sla-overdue-banner-content">
                        <i class="ph ph-warning-circle"></i>
                        <span>This case is overdue by {{ $slaData['days_overdue'] }} day(s). Immediate action required.</span>
                    </div>
                    <button @click="show = false" class="sla-overdue-banner-dismiss" aria-label="Dismiss overdue warning">
                        <i class="ph ph-x"></i>
                    </button>
                </div>
            @endif

            {{-- Case Overview Cards --}}
            <div class="overview-cards">
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="ph ph-calendar"></i>
                    </div>
                    <div class="overview-content">
                        <span class="overview-label">Next Hearing</span>
                        <span class="overview-value">
                            @php $nextHearing = $case->hearings()->where('scheduled_at', '>', now())->orderBy('scheduled_at')->first(); @endphp
                            {{ $nextHearing ? $nextHearing->scheduled_at->format('M d, Y') : 'None scheduled' }}
                        </span>
                    </div>
                </div>
                
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="ph ph-clock"></i>
                    </div>
                    <div class="overview-content">
                        <span class="overview-label">Days Active</span>
                        <span class="overview-value">{{ $case->no_of_days_in_barangay }} days</span>
                    </div>
                </div>
                
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="ph ph-folder"></i>
                    </div>
                    <div class="overview-content">
                        <span class="overview-label">Documents</span>
                        <span class="overview-value">{{ $case->documents->count() }} files</span>
                    </div>
                </div>
                
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="ph ph-chart-pie"></i>
                    </div>
                    <div class="overview-content">
                        <span class="overview-label">Compliance</span>
                        <span class="overview-value">{{ number_format($case->percentage_of_compliance, 1) }}%</span>
                    </div>
                </div>

            </div>

            {{-- Tabbed Content --}}
            <div class="case-tabs-container">
                <div class="case-tabs-nav">
                    <button class="case-tab active" data-tab="hearings">
                        <i class="ph ph-calendar"></i>
                        Hearings
                    </button>
                    <button class="case-tab" data-tab="documents">
                        <i class="ph ph-folder"></i>
                        Barangay Forms
                    </button>
                    <button class="case-tab" data-tab="pangkat">
                        <i class="ph ph-users-three"></i>
                        Pangkat
                    </button>
                </div>

                <div class="case-tabs-content">
                    {{-- Hearings Tab --}}
                    <div class="case-tab-panel active" id="hearings-panel">
                        <div class="tab-header">
                            <h3>Scheduled Hearings</h3>
                            <button onclick="Livewire.dispatch('openHearingForm', { caseId: {{ $case->id }} })" class="btn-add">
                                <i class="ph ph-plus"></i>
                                Add Hearing
                            </button>
                        </div>
                        <div class="hearings-list">
                            @forelse($case->hearings->sortBy('scheduled_at') as $hearing)
                                <div class="hearing-card">
                                    <div class="hearing-date-badge">
                                        <span class="hearing-day">{{ $hearing->scheduled_at->format('d') }}</span>
                                        <span class="hearing-month">{{ $hearing->scheduled_at->format('M') }}</span>
                                    </div>
                                    <div class="hearing-details">
                                        <div class="hearing-title">{{ ucfirst($hearing->hearing_type) }} Session</div>
                                        <div class="hearing-time">{{ $hearing->scheduled_at->format('g:i A') }} • {{ $hearing->location }}</div>
                                        @if($hearing->outcome)
                                            <div class="hearing-outcome">{{ $hearing->outcome }}</div>
                                        @endif
                                    </div>
                                    <div class="hearing-status">
                                        <span class="status-indicator status-{{ $hearing->status }}">{{ ucfirst($hearing->status) }}</span>
                                    </div>
                                    <div class="hearing-actions">
                                        <a href="{{ route('hearings.show', $hearing) }}" class="btn-link">View Details</a>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="ph ph-calendar-x"></i>
                                    <h4>No hearings scheduled</h4>
                                    <p>Schedule the first hearing to begin the mediation process.</p>
                                    <button onclick="Livewire.dispatch('openHearingForm', { caseId: {{ $case->id }} })" class="btn-primary">
                                        <i class="ph ph-calendar-plus"></i>
                                        Schedule First Hearing
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Documents Tab --}}
                    <div class="case-tab-panel" id="documents-panel">
                        <div class="tab-header">
                            <h3>Barangay Forms</h3>
                            <span style="font-size:0.8rem; color:var(--text-secondary);">Click Generate to preview a pre-filled printable form</span>
                        </div>
                        @include('cases.partials.document-export-panel')
                    </div>

                    {{-- Pangkat Tab --}}
                    <div class="case-tab-panel" id="pangkat-panel">
                        <div class="tab-header">
                            <h3>Pangkat Tagapagkasundo</h3>
                        </div>
                        @livewire('pangkat-assignment', ['case' => $case])
                    </div>

                    {{-- Progress & History Tab Removed --}}
                </div>
            </div>
        </div>

        {{-- Right Column - Sidebar --}}
        <div class="case-sidebar-column">
            
            {{-- Parties Information --}}
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <h3><i class="ph ph-users"></i> Parties</h3>
                </div>
                <div class="sidebar-card-body">
                    <div class="party-section">
                        <div class="party-label">Complainant</div>
                        <div class="party-name">{{ $case->complainant }}</div>
                        @if($case->complainant_address)
                            <div class="party-address">{{ $case->complainant_address }}</div>
                        @endif
                        @if($case->complainant_phone)
                            <div class="party-phone">{{ $case->complainant_phone }}</div>
                        @endif
                    </div>
                    
                    <div class="party-divider"></div>
                    
                    <div class="party-section">
                        <div class="party-label">Respondent</div>
                        <div class="party-name">{{ $case->respondent }}</div>
                        @if($case->respondent_address)
                            <div class="party-address">{{ $case->respondent_address }}</div>
                        @endif
                        @if($case->respondent_phone)
                            <div class="party-phone">{{ $case->respondent_phone }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related Cases --}}
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <h3><i class="ph ph-link"></i> Related Cases</h3>
                </div>
                <div class="sidebar-card-body">
                    @if(!$citizensLinked)
                        <p class="sidebar-empty">Link parties to citizens to find related cases.</p>
                    @elseif($relatedCases->isEmpty())
                        <p class="sidebar-empty">No related cases found.</p>
                    @else
                        @foreach($relatedCases as $relatedCase)
                            <a href="{{ route('cases.show', $relatedCase) }}" class="related-case-item">
                                <span class="related-case-number">{{ $relatedCase->case_number }}</span>
                                <span class="related-case-nature">{{ $relatedCase->nature_of_case }}</span>
                                <span class="related-case-status">{{ $relatedCase->status_label }}</span>
                                <span class="related-case-date">{{ $relatedCase->filed_date->format('M d, Y') }}</span>
                            </a>
                        @endforeach
                        @if($relatedCasesTotal > 5)
                            <p class="related-cases-more">and {{ $relatedCasesTotal - 5 }} more</p>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Case Details --}}
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <h3><i class="ph ph-info"></i> Case Details</h3>
                </div>
                <div class="sidebar-card-body">
                    @if($case->description)
                        <div class="detail-section">
                            <div class="detail-label">Description</div>
                            <div class="detail-value">{{ $case->description }}</div>
                        </div>
                    @endif
                    
                    @if($case->remarks)
                        <div class="detail-section">
                            <div class="detail-label">Remarks</div>
                            <div class="detail-value">{{ $case->remarks }}</div>
                        </div>
                    @endif
                    
                    <div class="detail-section">
                        <div class="detail-label">Filed By</div>
                        <div class="detail-value">{{ $case->filedBy->name ?? 'System' }}</div>
                    </div>
                </div>
            </div>

            {{-- Blotter Information --}}
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <h3><i class="ph ph-notebook"></i> Blotter Details</h3>
                </div>
                <div class="sidebar-card-body">
                    <div class="blotter-grid">
                        <div class="blotter-item">
                            <span class="blotter-label">Service of Summon</span>
                            <span class="blotter-value">{{ $case->date_of_service_summon?->format('M d, Y') ?? '—' }}</span>
                        </div>
                        
                        <div class="blotter-item">
                            <span class="blotter-label">1st Hearing</span>
                            <span class="blotter-value">{{ $case->date_of_first_hearing?->format('M d, Y') ?? '—' }}</span>
                        </div>
                        
                        <div class="blotter-item">
                            <span class="blotter-label">Settlement Date</span>
                            <span class="blotter-value">{{ $case->date_of_settlement?->format('M d, Y') ?? '—' }}</span>
                        </div>
                        
                        <div class="blotter-item">
                            <span class="blotter-label">Days Active</span>
                            <span class="blotter-value">{{ $case->no_of_days_in_barangay }} days</span>
                        </div>
                    </div>
                    
                    <div class="blotter-status-section">
                        <div class="status-grid">
                            <div class="status-item">
                                <span class="status-label">Settled</span>
                                <span class="status-indicator {{ $case->amicably_settled ? 'yes' : 'no' }}">
                                    {{ $case->amicably_settled ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            
                            <div class="status-item">
                                <span class="status-label">Mediation</span>
                                <span class="status-indicator {{ $case->mediation ? 'yes' : 'no' }}">
                                    {{ $case->mediation ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            
                            <div class="status-item">
                                <span class="status-label">Conciliation</span>
                                <span class="status-indicator {{ $case->conciliation ? 'yes' : 'no' }}">
                                    {{ $case->conciliation ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- HearingForm modal (Livewire) --}}
@livewire('hearing-form', ['case_id' => $case->id], 'hearing-form-case-'.$case->id)

{{-- Modern Styles --}}
@include('cases.partials.modern-styles')

{{-- Tab Switching Script --}}
@include('cases.partials.modern-scripts')

@endsection
