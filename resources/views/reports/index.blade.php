@extends('layouts.app')

@section('title', 'Reports | Lupon')
@section('page-title', 'Reports Overview')

@section('content')
    <div style="padding: 1.5rem;">
        
        <!-- Metrics Row -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.75rem;">
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
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="ph ph-certificate"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">CFA Issued</h3>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $cfaCases }}</p>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            
            {{-- Report Manager Livewire Component --}}
            @livewire('report-manager')

        </div>
    </div>
@endsection
