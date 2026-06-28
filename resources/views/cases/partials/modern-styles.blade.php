<style>
.case-show-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    background: #fafbfc;
    min-height: calc(100vh - 80px);
}

/* Modern Case Header */
.case-header-modern {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.case-header-left {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    flex: 1;
}

.case-icon-large {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.case-title-section {
    flex: 1;
}

.case-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.case-breadcrumb a {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.case-breadcrumb a:hover {
    color: #1d4ed8;
}

.case-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.75rem 0;
    line-height: 1.2;
}

.case-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.case-number {
    font-weight: 600;
    color: #3b82f6;
}

.case-separator {
    color: #d1d5db;
}

.case-header-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

.case-status-section .status-badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
}

.case-actions-header {
    display: flex;
    gap: 0.75rem;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: var(--bg-card);
    color: var(--text-secondary);
    border: 1px solid var(--border-light);
}

.btn-secondary:hover {
    background: var(--bg-hover);
    border-color: var(--border-medium);
}

/* Content Grid */
.case-content-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 2rem;
    align-items: start;
}

/* Overview Cards */
.overview-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.overview-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.overview-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f3f4f6;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.overview-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.overview-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.overview-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
}

/* Tabs */
.case-tabs-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
}

.case-tabs-nav {
    display: flex;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.case-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border: none;
    background: transparent;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 3px solid transparent;
}

.case-tab:hover {
    color: #374151;
    background: rgba(255, 255, 255, 0.5);
}

.case-tab.active {
    color: #3b82f6;
    background: white;
    border-bottom-color: #3b82f6;
}

.case-tabs-content {
    min-height: 400px;
}

.case-tab-panel {
    display: none;
    padding: 2rem;
}

.case-tab-panel.active {
    display: block;
}

.tab-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.tab-header h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.btn-add {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-add:hover {
    background: #e5e7eb;
}

/* Hearings List */
.hearings-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.hearing-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.hearing-card:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.hearing-date-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    flex-shrink: 0;
}

.hearing-day {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.hearing-month {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
}

.hearing-details {
    flex: 1;
}

.hearing-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.hearing-time {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.hearing-outcome {
    font-size: 0.875rem;
    color: #374151;
    font-style: italic;
}

.hearing-status {
    flex-shrink: 0;
}

.status-indicator {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-scheduled {
    background: #dbeafe;
    color: #1e40af;
}

.status-completed {
    background: #dcfce7;
    color: #166534;
}

.status-cancelled {
    background: #fef2f2;
    color: #991b1b;
}

.hearing-actions {
    flex-shrink: 0;
}

.btn-link {
    color: #3b82f6;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
}

.btn-link:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
    color: #d1d5db;
}

.empty-state h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 0.5rem 0;
}

.empty-state p {
    margin: 0 0 1.5rem 0;
}

/* Sidebar */
.case-sidebar-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sidebar-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.sidebar-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
}

.sidebar-card-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar-card-body {
    padding: 1.5rem;
}

/* Parties */
.party-section {
    margin-bottom: 1.5rem;
}

.party-section:last-child {
    margin-bottom: 0;
}

.party-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.5rem;
}

.party-name {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.party-address,
.party-phone {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.party-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 1.5rem 0;
}

/* Details */
.detail-section {
    margin-bottom: 1.5rem;
}

.detail-section:last-child {
    margin-bottom: 0;
}

.detail-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.5rem;
}

.detail-value {
    font-size: 0.875rem;
    color: #374151;
    line-height: 1.5;
}

/* Blotter Details */
.blotter-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.blotter-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.blotter-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.blotter-value {
    font-size: 0.875rem;
    font-weight: 500;
    color: #111827;
}

.blotter-status-section {
    border-top: 1px solid #e5e7eb;
    padding-top: 1.5rem;
}

.status-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
}

.status-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-align: center;
}

.status-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-indicator.yes {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: #166534;
    border: 1px solid #86efac;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-indicator.no {
    background: linear-gradient(135deg, #fef2f2, #fecaca);
    color: #991b1b;
    border: 1px solid #fca5a5;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .case-content-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .overview-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .case-show-container {
        padding: 1rem;
    }
    
    .case-header-modern {
        flex-direction: column;
        align-items: stretch;
        gap: 1.5rem;
        padding: 1.5rem;
    }
    
    .case-header-left {
        flex-direction: column;
        gap: 1rem;
    }
    
    .case-icon-large {
        width: 56px;
        height: 56px;
        font-size: 1.75rem;
    }
    
    .case-title {
        font-size: 1.5rem;
    }
    
    .case-actions-header {
        flex-direction: column;
        width: 100%;
    }
    
    .overview-cards {
        grid-template-columns: 1fr;
    }
    
    .case-tabs-nav {
        flex-wrap: wrap;
    }
    
    .case-tab {
        flex: 1 1 50%;
        min-width: 120px;
    }
    
    .case-tab-panel {
        padding: 1.5rem;
    }
    
    .hearing-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .blotter-grid {
        grid-template-columns: 1fr;
    }
    
    .status-grid {
        grid-template-columns: 1fr;
    }
}

    /* Case Detail Dark Mode overrides */
    [data-theme='dark'] .case-show-container { background: var(--bg-page); }
    [data-theme='dark'] .case-header-modern,
    [data-theme='dark'] .overview-card,
    [data-theme='dark'] .case-tabs-container,
    [data-theme='dark'] .sidebar-card,
    [data-theme='dark'] .kp-step-content,
    [data-theme='dark'] .kp-summary {
        background: var(--bg-card);
        border-color: #334155;
    }
    [data-theme='dark'] .case-tabs-nav,
    [data-theme='dark'] .sidebar-card-header,
    [data-theme='dark'] .tab-header,
    [data-theme='dark'] .kp-step-header {
        background: #1e293b;
        border-bottom-color: #334155;
    }
    [data-theme='dark'] .case-title,
    [data-theme='dark'] .overview-value,
    [data-theme='dark'] .tab-header h3,
    [data-theme='dark'] .sidebar-card-header h3,
    [data-theme='dark'] .party-name,
    [data-theme='dark'] .detail-value,
    [data-theme='dark'] .hearing-title,
    [data-theme='dark'] .kp-step-label,
    [data-theme='dark'] .kp-summary-value {
        color: var(--text-primary);
    }
    [data-theme='dark'] .case-tab.active {
        background: var(--bg-card);
        border-bottom-color: var(--accent-blue);
        color: var(--accent-blue);
    }
    [data-theme='dark'] .hearing-card,
    [data-theme='dark'] .doc-item,
    [data-theme='dark'] .kp-step-doc {
        background: #1e293b;
        border-color: #334155;
    }
    [data-theme='dark'] .hearing-date-badge {
        background: #0f172a;
        border-color: #334155;
    }
    [data-theme='dark'] .hearing-day { color: var(--text-primary); }
</style>

<style>
/* ── KP Document Flow ─────────────────────────────────────────── */
.kp-flow-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Legend */
.kp-legend {
    display: flex;
    gap: 1.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: #6b7280;
}

.kp-legend-item {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.kp-legend-item.kp-done  { color: #16a34a; }
.kp-legend-item.kp-active { color: #2563eb; }
.kp-legend-item.kp-pending { color: #9ca3af; }

/* Steps container */
.kp-steps {
    display: flex;
    flex-direction: column;
    gap: 0;
}

/* Individual step */
.kp-step {
    display: grid;
    grid-template-columns: 28px 1fr;
    grid-template-rows: auto auto;
    column-gap: 1rem;
    position: relative;
}

/* Connector line between steps */
.kp-connector {
    grid-column: 1;
    grid-row: 1;
    width: 2px;
    height: 1.25rem;
    margin: 0 auto;
    border-radius: 2px;
}

.kp-connector-done    { background: #16a34a; }
.kp-connector-pending { background: #e5e7eb; }

/* Node circle */
.kp-node {
    grid-column: 1;
    grid-row: 2;
    display: flex;
    justify-content: center;
    padding-top: 0.25rem;
}

.kp-node-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.kp-step-done   .kp-node-circle { background: #16a34a; color: white; }
.kp-step-active .kp-node-circle { background: #2563eb; color: white; box-shadow: 0 0 0 4px rgba(37,99,235,0.15); }
.kp-step-pending .kp-node-circle { background: #f3f4f6; color: #9ca3af; border: 2px solid #e5e7eb; }

.kp-step-number { font-size: 0.75rem; }

/* Step content card */
.kp-step-content {
    grid-column: 2;
    grid-row: 2;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.kp-step-done    .kp-step-content { border-color: #bbf7d0; }
.kp-step-active  .kp-step-content { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(37,99,235,0.08); }
.kp-step-pending .kp-step-content { opacity: 0.65; }

.kp-step-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.kp-step-done   .kp-step-header { background: #f0fdf4; border-bottom-color: #bbf7d0; }
.kp-step-active .kp-step-header { background: #eff6ff; border-bottom-color: #93c5fd; }

.kp-step-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.kp-step-done    .kp-step-icon { background: #dcfce7; color: #16a34a; }
.kp-step-active  .kp-step-icon { background: #dbeafe; color: #2563eb; }
.kp-step-pending .kp-step-icon { background: #f3f4f6; color: #9ca3af; }

.kp-step-title-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.kp-step-label {
    font-size: 0.9rem;
    font-weight: 700;
    color: #111827;
}

.kp-step-date {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Badges */
.kp-step-badge {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.25rem 0.6rem;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    flex-shrink: 0;
}

.kp-badge-done    { background: #dcfce7; color: #15803d; }
.kp-badge-active  { background: #dbeafe; color: #1d4ed8; }
.kp-badge-pending { background: #f3f4f6; color: #9ca3af; }

/* Step body */
.kp-step-body {
    padding: 0.875rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.kp-step-note {
    font-size: 0.85rem;
    color: #374151;
    line-height: 1.5;
}

.kp-step-doc {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    color: #6b7280;
    background: #f3f4f6;
    padding: 0.3rem 0.65rem;
    border-radius: 6px;
    width: fit-content;
}

.kp-step-doc i { font-size: 0.85rem; }

/* Spin animation for active icon */
@keyframes kp-spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
.kp-spin { animation: kp-spin 1.5s linear infinite; }

/* Summary footer */
.kp-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
}

.kp-summary-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.kp-summary-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.kp-summary-value {
    font-size: 0.9rem;
    font-weight: 700;
    color: #111827;
}

.kp-sla-on-track    { color: #16a34a; }
.kp-sla-approaching { color: #d97706; }
.kp-sla-overdue     { color: #dc2626; }
.kp-sla-resolved    { color: #2563eb; }
.kp-sla-post-mediation { color: #7c3aed; }

@media (max-width: 768px) {
    .kp-summary { grid-template-columns: 1fr 1fr; }
    .kp-legend  { flex-wrap: wrap; gap: 0.75rem; }
}

    /* Case Detail Dark Mode overrides */
    [data-theme='dark'] .case-show-container { background: var(--bg-page); }
    [data-theme='dark'] .case-header-modern,
    [data-theme='dark'] .overview-card,
    [data-theme='dark'] .case-tabs-container,
    [data-theme='dark'] .sidebar-card,
    [data-theme='dark'] .kp-step-content,
    [data-theme='dark'] .kp-summary {
        background: var(--bg-card);
        border-color: #334155;
    }
    [data-theme='dark'] .case-tabs-nav,
    [data-theme='dark'] .sidebar-card-header,
    [data-theme='dark'] .tab-header,
    [data-theme='dark'] .kp-step-header {
        background: #1e293b;
        border-bottom-color: #334155;
    }
    [data-theme='dark'] .case-title,
    [data-theme='dark'] .overview-value,
    [data-theme='dark'] .tab-header h3,
    [data-theme='dark'] .sidebar-card-header h3,
    [data-theme='dark'] .party-name,
    [data-theme='dark'] .detail-value,
    [data-theme='dark'] .hearing-title,
    [data-theme='dark'] .kp-step-label,
    [data-theme='dark'] .kp-summary-value {
        color: var(--text-primary);
    }
    [data-theme='dark'] .case-tab.active {
        background: var(--bg-card);
        border-bottom-color: var(--accent-blue);
        color: var(--accent-blue);
    }
    [data-theme='dark'] .hearing-card,
    [data-theme='dark'] .doc-item,
    [data-theme='dark'] .kp-step-doc {
        background: #1e293b;
        border-color: #334155;
    }
    [data-theme='dark'] .hearing-date-badge {
        background: #0f172a;
        border-color: #334155;
    }
    [data-theme='dark'] .hearing-day { color: var(--text-primary); }

    /* Blotter Details dark mode */
    [data-theme='dark'] .blotter-value {
        color: var(--text-primary);
    }
    [data-theme='dark'] .blotter-label,
    [data-theme='dark'] .status-label {
        color: var(--text-muted);
    }
    [data-theme='dark'] .blotter-status-section {
        border-top-color: #334155;
    }
    [data-theme='dark'] .detail-label,
    [data-theme='dark'] .party-label,
    [data-theme='dark'] .party-address,
    [data-theme='dark'] .party-phone {
        color: var(--text-muted);
    }
    [data-theme='dark'] .party-divider {
        border-color: #334155;
    }

    /* SLA Overdue Banner override style */
    .sla-overdue-banner {
        position: relative !important;
        top: auto !important;
        right: auto !important;
        animation: none !important;
        max-width: 100% !important;
        width: 100% !important;
        margin-bottom: 1.5rem !important;
        pointer-events: auto !important;
        box-shadow: none !important;
    }
    .sla-overdue-banner-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-grow: 1;
    }
    .sla-overdue-banner-dismiss {
        background: none;
        border: none;
        color: currentColor;
        cursor: pointer;
        padding: 0;
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        opacity: 0.6;
        transition: opacity 0.15s;
        font-size: 1.1rem;
    }
    .sla-overdue-banner-dismiss:hover {
        opacity: 1;
    }
</style>



