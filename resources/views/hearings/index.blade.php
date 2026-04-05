@extends('layouts.app')

@section('title', 'Hearings Management | Lupon')
@section('page-title', 'Hearings')

@section('content')
<div class="dashboard-grid" x-data="{ view: 'calendar' }">
    {{-- Single Global Instance of Hearing Form --}}
    @livewire('hearing-form', [], 'hearing-form-main')
    
    <!-- Header Section -->
    <div class="full-width-table" style="margin-bottom: 1.5rem;">
        <div class="kpi-card" style="display: flex; justify-content: space-between; align-items: center; padding: 2rem; border-left: 4px solid var(--accent-blue);">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Scheduling & Hearings</h1>
                <p style="color: var(--text-secondary); font-size: 0.9375rem;">Manage mediation, conciliation, and arbitration schedules for Barangay Bula.</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="saas-tabs" style="background: var(--gray-100); padding: 4px; border-radius: 12px; display: flex; gap: 4px; border: 1px solid var(--gray-200);">
                    <button @click="view = 'calendar'" :class="view === 'calendar' ? 'saas-tab-active' : ''" class="saas-tab" style="padding: 8px 16px; font-weight: 700; font-size: 0.8125rem;">
                        <i class="ph ph-calendar"></i> Calendar View
                    </button>
                    <button @click="view = 'list'" :class="view === 'list' ? 'saas-tab-active' : ''" class="saas-tab" style="padding: 8px 16px; font-weight: 700; font-size: 0.8125rem;">
                        <i class="ph ph-list"></i> Detailed List
                    </button>
                </div>
                <button @click="Livewire.dispatch('openHearingForm')" class="btn-primary" style="height: 48px; padding: 0 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
                    <i class="ph-bold ph-plus"></i>
                    Schedule New Hearing
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div x-show="view === 'calendar'" class="full-width-table" x-transition:enter="fade-in"
         @click.outside=""
         x-effect="if (view === 'calendar') { $nextTick(() => { if (window.hearingCalendar) window.hearingCalendar.updateSize(); }) }">
        <div class="card" style="border: none; box-shadow: var(--shadow-lg);">
            @livewire('hearing-calendar')
        </div>
    </div>

    <!-- List View -->
    <div x-show="view === 'list'" class="full-width-table" x-transition:enter="fade-in" style="display: none;">
        <div class="card" style="border: none; box-shadow: var(--shadow-lg);">
            @livewire('hearing-list')
        </div>
    </div>
</div>

<style>
    .saas-tab {
        background: transparent;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .saas-tab-active {
        background: var(--bg-card);
        color: var(--accent-blue);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* FullCalendar Dark Mode Refinement */
    [data-theme="dark"] .fc {
        --fc-border-color: #1e293b;
        --fc-page-bg-color: #0f172a;
    }
    [data-theme="dark"] .fc-theme-standard td, 
    [data-theme="dark"] .fc-theme-standard th {
        border-color: #334155 !important;
    }
    [data-theme="dark"] .fc-col-header-cell {
        background-color: #1e293b;
    }
    [data-theme="dark"] .fc-daygrid-day-number,
    [data-theme="dark"] .fc-col-header-cell-cushion {
        color: var(--text-primary);
    }
    [data-theme="dark"] .fc-day-today {
        background: rgba(96, 165, 250, 0.05) !important;
    }
    [data-theme="dark"] .fc-scrollgrid {
        border-color: #334155;
    }
    [data-theme="dark"] .fc-button-primary {
        background-color: #1e293b;
        border-color: #334155;
        color: var(--text-primary);
    }
    [data-theme="dark"] .fc-button-primary:hover {
        background-color: #334155;
    }
    [data-theme="dark"] .fc-toolbar-title {
        color: var(--text-primary);
    }
    [data-theme="dark"] .saas-tab-active {
        background: #1e293b;
        color: var(--accent-blue);
        border: 1px solid #334155;
    }
    [data-theme="dark"] .saas-tabs {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
</style>

@push('scripts')
<script>
function openScheduleModal() {
    if (window.Livewire) {
        window.Livewire.dispatch('openHearingForm');
    }
}

// Auto-open modal with pre-filled case if coming from "Schedule Next Meeting"
document.addEventListener('livewire:initialized', () => {
    @if(request()->has('schedule_for'))
        Livewire.dispatch('openHearingForm', { caseId: {{ (int) request('schedule_for') }} });
    @endif
});
</script>
@endpush
@endsection
