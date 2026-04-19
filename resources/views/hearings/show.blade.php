@extends('layouts.app')

@section('title', 'Hearing Details | Lupon')
@section('page-title', 'Hearings — Detail')

@section('content')
    <div style="padding: 2rem 32px;">

        <!-- Breadcrumbs & Status Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <nav
                    style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.75rem;">
                    <a href="{{ route('hearings.index') }}"
                        style="color: var(--text-muted); text-decoration: none; transition: color 0.2s;"
                        onmouseover="this.style.color='var(--accent-blue)'"
                        onmouseout="this.style.color='var(--text-muted)'">Hearings</a>
                    <i class="ph ph-caret-right" style="font-size: 0.75rem;"></i>
                    <span style="color: var(--text-primary);">Detail View</span>
                </nav>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">
                        {{ ucfirst($hearing->hearing_type) }} Hearing
                    </h1>
                    <div style="display: flex; gap: 0.5rem;">
                        @php
                            $statusColor = match ($hearing->status) {
                                'completed' => 'var(--success)',
                                'cancelled', 'failed' => 'var(--danger)',
                                default => 'var(--accent-blue)',
                            };
                            $statusBg = match ($hearing->status) {
                                'completed' => 'var(--success-light)',
                                'cancelled', 'failed' => 'var(--danger-light)',
                                default => 'var(--accent-light)',
                            };
                        @endphp
                        <span
                            style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 6px 14px; border-radius: 99px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid {{ $statusColor }}20;">
                            {{ $hearing->status }}
                        </span>
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button onclick="Livewire.dispatch('editHearing', { hearingId: {{ $hearing->id }} })" class="btn-secondary"
                    style="display: flex; align-items: center; gap: 0.625rem; height: 42px;">
                    <i class="ph ph-pencil"></i> Edit Details
                </button>
                <a href="{{ route('hearings.index') }}" class="btn-secondary"
                    style="display: flex; align-items: center; gap: 0.625rem; height: 42px; text-decoration: none;">
                    <i class="ph ph-arrow-left"></i> Back to Schedule
                </a>
            </div>
        </div>

        {{-- HearingForm modal (for editing) --}}
        @livewire('hearing-form', [], 'hearing-form-show')

        {{-- Main Interaction Area (Logistics, Attendance, outcome, and Sidebar) --}}
        @livewire('hearing-attendance', ['hearing' => $hearing])

    </div>
@endsection