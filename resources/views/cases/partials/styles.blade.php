<style>
/* Case Header Enhanced */
.case-header-enhanced {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-light, #e5e7eb);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.case-header-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.case-number-section {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.case-header-enhanced .case-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--accent-blue, #3b82f6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.case-details .case-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted, #94a3b8);
    letter-spacing: 0.05em;
}

.case-details .case-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary, #1e293b);
    margin: 0;
    line-height: 1.2;
}

.case-details .case-date {
    font-size: 0.8rem;
    color: var(--text-secondary, #64748b);
}

.case-status-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.case-progress {
    min-width: 200px;
}

.case-progress .progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary, #64748b);
    margin-bottom: 4px;
}

.case-progress .progress-percentage {
    font-weight: 700;
    color: var(--text-primary, #1e293b);
}

.case-progress .progress-bar {
    height: 6px;
    background: var(--gray-100, #f3f4f6);
    border-radius: 3px;
    overflow: hidden;
}

.case-progress .progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.case-progress .progress-note {
    font-size: 0.65rem;
    color: var(--text-muted, #94a3b8);
    margin-top: 2px;
}

/* Quick Actions */
.case-actions {
    border-top: 1px solid var(--gray-100, #f3f4f6);
    padding-top: 1rem;
}

.action-group .action-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted, #94a3b8);
    margin-bottom: 0.5rem;
    display: block;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: var(--gray-50, #f9fafb);
    border: 1px solid var(--border-light, #e5e7eb);
    border-radius: 8px;
    color: var(--text-secondary, #64748b);
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
}

.action-btn:hover {
    background: var(--accent-blue, #3b82f6);
    border-color: var(--accent-blue, #3b82f6);
    color: white;
    transform: translateY(-1px);
}

/* Content Layout */
.case-content-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.5rem;
    align-items: start;
}

@media (max-width: 1024px) {
    .case-content-layout {
        grid-template-columns: 1fr;
    }
    
    .case-sidebar {
        order: -1; /* On mobile, show metadata above tabs */
    }
}

/* Info Cards */
.info-card, .parties-card {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--border-light, #e5e7eb);
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
}

.info-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
}

.info-card-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-secondary, #64748b);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.info-card-body {
    padding: 1.25rem;
}

.case-nature {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
}

.nature-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-muted, #94a3b8);
}

.nature-value {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-primary, #1e293b);
    background: var(--gray-50, #f9fafb);
    padding: 4px 12px;
    border-radius: 6px;
}

.description-title {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-muted, #94a3b8);
    margin-bottom: 0.25rem;
}

.description-text {
    font-size: 0.875rem;
    color: var(--text-secondary, #64748b);
    line-height: 1.6;
}

/* Parties Grid */
.parties-grid {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0;
    padding: 1.25rem;
}

.party-section {
    padding: 1rem;
}

.party-section.complainant {
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
    padding-bottom: 2rem;
}

.party-section.respondent {
    padding-top: 2rem;
}

.party-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.party-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.complainant-icon {
    background: #dbeafe;
    color: var(--accent-blue, #3b82f6);
}

.respondent-icon {
    background: #fee2e2;
    color: #ef4444;
}

.party-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted, #94a3b8);
    display: block;
}

.party-subtitle {
    font-size: 0.7rem;
    color: var(--text-muted, #94a3b8);
}

.party-name {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text-primary, #1e293b);
    margin-bottom: 0.5rem;
}

.party-info .info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--text-secondary, #64748b);
    margin-bottom: 0.25rem;
}

.party-info .info-item i {
    color: var(--text-muted, #94a3b8);
    font-size: 0.875rem;
}

.vs-divider {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    padding: 10px;
    z-index: 10;
}

.vs-divider span {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--text-muted, #94a3b8);
    background: var(--gray-100, #f3f4f6);
    padding: 4px 10px;
    border-radius: 20px;
}

/* Sidebar Tabs */
.sidebar-tabs-container {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--border-light, #e5e7eb);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
}

.tab-nav {
    display: flex;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
    background: var(--gray-50, #f9fafb);
    padding: 0;
}

.tab-btn {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    padding: 12px 8px;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--text-muted, #94a3b8);
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}

.tab-btn i {
    font-size: 1.1rem;
}

.tab-btn:hover {
    color: var(--accent-blue, #3b82f6);
    background: white;
}

.tab-btn.active {
    color: var(--accent-blue, #3b82f6);
    border-bottom-color: var(--accent-blue, #3b82f6);
    background: white;
}

.tab-content {
    min-height: 300px;
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

.tab-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
}

.tab-panel-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-secondary, #64748b);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tab-action-btn {
    background: none;
    border: 1px solid var(--border-light, #e5e7eb);
    border-radius: 6px;
    padding: 6px;
    color: var(--text-secondary, #64748b);
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.tab-action-btn:hover {
    background: var(--accent-blue, #3b82f6);
    border-color: var(--accent-blue, #3b82f6);
    color: white;
}

.tab-panel-body {
    padding: 1rem 1.25rem;
}

/* Hearing Items */
.hearing-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border: 1px solid var(--gray-100, #f3f4f6);
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: background 0.15s;
}

.hearing-item:hover {
    background: var(--gray-50, #f9fafb);
}

.hearing-date {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 40px;
    background: var(--accent-blue, #3b82f6);
    color: white;
    border-radius: 8px;
    padding: 6px 8px;
}

.hearing-day {
    font-size: 1rem;
    font-weight: 800;
    line-height: 1;
}

.hearing-month {
    font-size: 0.6rem;
    font-weight: 600;
    text-transform: uppercase;
}

.hearing-info {
    flex: 1;
}

.hearing-type {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-primary, #1e293b);
    display: block;
}

.hearing-time {
    font-size: 0.75rem;
    color: var(--text-muted, #94a3b8);
}

.hearing-status {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    text-transform: uppercase;
    white-space: nowrap;
}

.hearing-status.scheduled {
    background: #dbeafe;
    color: #2563eb;
}

.hearing-status.completed {
    background: #dcfce7;
    color: #16a34a;
}

.hearing-status.cancelled, .hearing-status.postponed {
    background: #fee2e2;
    color: #dc2626;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 2rem 1rem;
    color: var(--text-muted, #94a3b8);
    text-align: center;
}

.empty-state i {
    font-size: 2rem;
    opacity: 0.5;
}

.empty-state span {
    font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 768px) {
    .case-header-main {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .case-status-section {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }

    .case-progress {
        width: 100%;
    }

    .parties-grid {
        grid-template-columns: 1fr;
    }

    .party-section.complainant {
        border-right: none;
        border-bottom: 1px solid var(--gray-100, #f3f4f6);
        padding-right: 1rem;
        padding-bottom: 1.25rem;
    }

    .party-section.respondent {
        padding-left: 1rem;
        padding-top: 1.25rem;
    }

    .vs-divider {
        padding: 0.5rem 0;
    }
}
</style>

/* Blotter Information Styles */
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
    color: var(--text-muted, #94a3b8);
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.blotter-value {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary, #1e293b);
}

.blotter-status-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: var(--gray-50, #f9fafb);
    border-radius: 8px;
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
    color: var(--text-muted, #94a3b8);
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-indicator {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    min-width: 50px;
    text-align: center;
}

.status-indicator.yes {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: #166534;
    border: 1px solid #86efac;
}

.status-indicator.no {
    background: linear-gradient(135deg, #fef2f2, #fecaca);
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.blotter-remarks {
    border-top: 1px solid var(--border-light, #e5e7eb);
    padding-top: 1rem;
}

.blotter-remarks-text {
    font-size: 0.875rem;
    color: var(--text-secondary, #64748b);
    line-height: 1.5;
    margin: 0.5rem 0 0 0;
    font-style: italic;
}

@media (max-width: 768px) {
    .blotter-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .blotter-status-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}

    /* Case Record Legacy Partials - Dark Mode Overrides */
    [data-theme='dark'] .case-header-enhanced,
    [data-theme='dark'] .info-card,
    [data-theme='dark'] .parties-card,
    [data-theme='dark'] .sidebar-tabs-container {
        background: var(--bg-card);
        border-color: #334155;
    }
    [data-theme='dark'] .info-card-header,
    [data-theme='dark'] .tab-nav,
    [data-theme='dark'] .tab-panel-header {
        background: #1e293b;
        border-bottom-color: #334155;
    }
    [data-theme='dark'] .case-number,
    [data-theme='dark'] .nature-value,
    [data-theme='dark'] .party-name,
    [data-theme='dark'] .hearing-type {
        color: var(--text-primary);
    }
    [data-theme='dark'] .nature-value {
        background: #1e293b;
    }
    [data-theme='dark'] .vs-divider {
        background: #0f172a;
    }
    [data-theme='dark'] .vs-divider span {
        background: #1e293b;
        color: var(--text-muted);
    }
    [data-theme='dark'] .tab-btn.active,
    [data-theme='dark'] .tab-btn:hover {
        background: #1e293b;
        color: var(--accent-blue);
    }
    [data-theme='dark'] .hearing-item {
        border-color: #334155;
    }
    [data-theme='dark'] .hearing-item:hover {
        background: #1e293b;
    }
    [data-theme='dark'] .blotter-status-grid {
        background: #1e293b;
    }
</style>
