{{-- Enhanced Case Header --}}
<div class="case-header-enhanced">
    <div class="case-header-main">
        <div class="case-number-section">
            <div class="case-icon">
                <i class="ph ph-notebook"></i>
            </div>
            <div class="case-details">
                <span class="case-label">Case Number</span>
                <h1 class="case-number">{{ $case->case_number }}</h1>
                <span class="case-date">Filed {{ $case->filed_date->format('F d, Y') }}</span>
            </div>
        </div>

        <div class="case-status-section">
            <span class="status-badge {{ $case->status_badge_class }}" style="font-size: 0.85rem; padding: 6px 14px;">
                {{ $case->status_label }}
            </span>

            <div class="case-progress">
                @php $docsPercent = $case->docs_completeness; @endphp
                <div class="progress-label">
                    <span>Case Folder</span>
                    <span class="progress-percentage">{{ $docsPercent }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $docsPercent }}%; background-color: {{ $docsPercent >= 100 ? '#10b981' : '#f59e0b' }};"></div>
                </div>
                <span class="progress-note">MOV #2 - Digital Case Folder</span>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="case-actions">
        <div class="action-group">
            <span class="action-label">Actions</span>
            <div class="action-buttons">
                <a href="{{ route('cases.edit', $case) }}" class="action-btn" title="Edit Case">
                    <i class="ph ph-pencil-simple"></i>
                    <span>Edit</span>
                </a>
                <button onclick="Livewire.dispatch('openHearingForm', { caseId: {{ $case->id }} })" class="action-btn" title="Schedule Hearing">
                    <i class="ph ph-calendar-plus"></i>
                    <span>Schedule</span>
                </button>
                <a href="{{ route('cases.index') }}" class="action-btn" title="Back to List">
                    <i class="ph ph-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>
</div>
