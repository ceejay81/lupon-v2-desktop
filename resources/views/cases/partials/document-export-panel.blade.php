@php
$docGroups = [
    'Stage 1 — Recording' => [
        [
            'key'   => 'status-of-case',
            'title' => 'Status of Case',
            'desc'  => 'Full case record with parties, hearing history, pangkat, and current status summary.',
            'icon'  => 'ph-clipboard-text',
            'color' => '#0891b2',
            'route' => route('cases.export.status-of-case', $case),
        ],
    ],
    'Stage 2 — Mediation' => [
        [
            'key'   => 'invitation-notice',
            'title' => 'Invitation Notice',
            'desc'  => 'Informal invite to the respondent for a dialogue at the barangay office.',
            'icon'  => 'ph-envelope',
            'color' => '#7c3aed',
            'route' => route('cases.export.invitation-notice', $case),
        ],
        [
            'key'   => 'notice-of-hearing',
            'title' => 'Notice of Hearing',
            'desc'  => 'Formal notice to complainant to appear before the Punong Barangay for mediation.',
            'icon'  => 'ph-bell',
            'color' => '#2563eb',
            'route' => route('cases.export.notice-of-hearing', $case),
        ],
        [
            'key'   => 'summon',
            'title' => 'Summons (KP Form 09)',
            'desc'  => 'Legal summons to the respondent to appear. Includes Officer\'s Return section.',
            'icon'  => 'ph-gavel',
            'color' => '#dc2626',
            'route' => route('cases.export.summon', $case),
        ],
    ],
    'Stage 3 — Settlement' => [
        [
            'key'   => 'amicable-settlement',
            'title' => 'Amicable Settlement',
            'desc'  => 'Written settlement agreement signed by both parties, attested by the Punong Barangay.',
            'icon'  => 'ph-handshake',
            'color' => '#16a34a',
            'route' => route('cases.export.amicable-settlement', $case),
        ],
        [
            'key'   => 'kasabutan',
            'title' => 'Kasabutan',
            'desc'  => 'Settlement agreement written in Cebuano/Bisaya dialect with full party details.',
            'icon'  => 'ph-scroll',
            'color' => '#059669',
            'route' => route('cases.export.kasabutan', $case),
        ],
    ],
    'Stage 4 — Final Issuance' => [
        [
            'key'   => 'certificate-to-file-action',
            'title' => 'Certificate to File Action (KP Form 20)',
            'desc'  => 'Issued when conciliation fails. Required prerequisite to file a case in court.',
            'icon'  => 'ph-certificate',
            'color' => '#d97706',
            'route' => route('cases.export.certificate-to-file-action', $case),
        ],
    ],
];
@endphp

<div class="doc-export-wrapper">
    @foreach($docGroups as $groupName => $docs)
        <div class="doc-stage-group">
            <h4 class="doc-stage-title">{{ $groupName }}</h4>
            
            <div class="doc-export-grid">
                @foreach($docs as $doc)
                @php $isDone = isset($case->completed_steps[$doc['key']]); @endphp
                <div class="doc-export-card {{ $isDone ? 'is-completed' : '' }}">

                    {{-- Icon --}}
                    <div class="doc-export-icon" style="background: {{ $doc['color'] }}18; color: {{ $doc['color'] }};">
                        <i class="ph {{ $doc['icon'] }}"></i>
                    </div>

                    {{-- Info --}}
                    <div class="doc-export-info">
                        <div class="doc-export-title">{{ $doc['title'] }}</div>
                        <div class="doc-export-desc">{{ $doc['desc'] }}</div>
                    </div>

                    {{-- Actions --}}
                    <div class="doc-export-actions">
                        <button type="button" 
                                onclick="toggleStep('{{ $doc['key'] }}', {{ $isDone ? 'false' : 'true' }}, this)" 
                                class="doc-btn {{ $isDone ? 'doc-btn-done' : 'doc-btn-confirm' }}" 
                                style="{{ !$isDone ? 'color: '.$doc['color'].'; border-color: '.$doc['color'].';' : '' }}">
                            @if($isDone)
                                <i class="ph ph-check-circle"></i> Done
                            @else
                                <i class="ph ph-check"></i> Confirm
                            @endif
                        </button>

                        <a href="{{ $doc['route'] }}" class="doc-btn doc-btn-view" style="background: {{ $doc['color'] }}; color: white; border: none;">
                            <i class="ph ph-file-text"></i>
                            Open & Preview
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<style>
.doc-export-wrapper {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.doc-stage-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.doc-stage-title {
    font-size: 0.85rem;
    font-weight: 800;
    color: #4b5563;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f1f5f9;
    margin-bottom: 0.25rem;
}

.doc-export-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.doc-export-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.2s;
}

.doc-export-card.is-completed {
    background: #f0fdf4;
    border-color: #bbf7d0;
}

.doc-export-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    background: #fff;
}

.doc-export-card.is-completed:hover {
    background: #f0fdf4;
    border-color: #86efac;
}

.doc-export-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.doc-export-info {
    flex: 1;
    min-width: 0;
}

.doc-export-stage {
    font-size: 0.7rem;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.15rem;
}

.doc-export-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.2rem;
}

.doc-export-desc {
    font-size: 0.78rem;
    color: #6b7280;
    line-height: 1.4;
}

.doc-export-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.doc-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
    border: 1px solid transparent;
    cursor: pointer;
}

.doc-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.doc-btn:active {
    transform: translateY(0);
}

.doc-btn-confirm {
    background: white;
    border: 1px solid #d1d5db;
}

.doc-btn-done {
    background: #10b981;
    color: white;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.doc-btn-view {
    min-width: 140px;
    justify-content: center;
}

@media (max-width: 640px) {
    .doc-export-card    { flex-direction: column; align-items: flex-start; }
    .doc-export-actions { width: 100%; margin-top: 0.5rem; }
    .doc-btn            { flex: 1; justify-content: center; }
}

    /* Document Export Dark Mode */
    [data-theme='dark'] .doc-stage-title {
        color: var(--text-primary);
        border-bottom-color: #334155;
    }
    [data-theme='dark'] .doc-export-card {
        background: #1e293b;
        border-color: #334155;
    }
    [data-theme='dark'] .doc-export-card:hover {
        background: #1e293b;
        border-color: #475569;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    [data-theme='dark'] .doc-export-title {
        color: var(--text-primary);
    }
    [data-theme='dark'] .doc-export-desc {
        color: var(--text-secondary);
    }
    [data-theme='dark'] .doc-btn-confirm {
        background: #1e293b;
        border-color: #475569;
        color: var(--text-secondary);
    }
    [data-theme='dark'] .doc-export-card.is-completed {
        background: #022c22;
        border-color: #065f46;
    }
</style>

<script>
window.toggleStep = function(step, done, btn) {
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="ph ph-circle-notch ph-spin"></i> ...';
    btn.disabled = true;

    // Use a more robust check for CSRF token
    const tokenTag = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenTag ? tokenTag.getAttribute('content') : '';

    fetch("{{ route('cases.export.toggle-step-completion', $case) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ step: step, done: done })
    })
    .then(response => {
        if (!response.ok) throw new Error('Session Error');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="ph ph-check"></i>';
            window.location.reload();
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.warn('Confirmation error:', error);
        btn.innerHTML = originalContent; // Reset immediately
        btn.disabled = false;
        if (!csrfToken) alert('Missing Security Token (CSRF). Try refreshing.');
    });
};
</script>
