@extends('layouts.app')

@section('title', 'Digital Archives | Lupon')
@section('page-title', 'Digital Archives')

@section('content')
<div class="explorer-wrapper" x-data="{ 
    currentYear: '{{ collect($archive)->keys()->first() ?? now()->year }}',
    search: '',
    selectedMonth: null
}">
    <div class="explorer-container">
        <!-- LEFT: Folder Sidebar (Year Archive) -->
        <aside class="folder-sidebar">
            <div class="sidebar-section">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                    <h3><i class="ph-bold ph-calendar"></i> ARCHIVE YEARS</h3>
                </div>
                <ul class="folder-tree">
                    @forelse(array_keys($archive) as $year)
                        <li class="folder-item" :class="currentYear === '{{ $year }}' ? 'active' : ''" @click="currentYear = '{{ $year }}'; selectedMonth = null">
                            <i class="ph-fill ph-folder"></i>
                            <span>FY {{ $year }}</span>
                        </li>
                    @empty
                        <div style="padding: 1rem; color: var(--text-muted); font-size: 0.8125rem;">No archives found.</div>
                    @endforelse
                    
                    <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--border-color);">
                    
                    <a href="{{ route('reports.index') }}" class="ref-link">
                        <i class="ph ph-arrow-left"></i>
                        Back to Generator
                    </a>
                </ul>
            </div>

            <div class="sidebar-section" style="margin-top: auto;">
                <div class="cabinet-stats">
                    <p><strong>Total Archive Units</strong></p>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.75rem;">
                        <span>Finalized Reports:</span>
                        <span>{{ collect($archive)->flatten(2)->pluck('reports')->filter()->flatten()->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.25rem; font-size: 0.75rem;">
                        <span>Settled Cases:</span>
                        <span>{{ collect($archive)->flatten(2)->pluck('cases')->filter()->flatten()->count() }}</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- RIGHT: Main Explorer Area -->
        <main class="explorer-main">
            <!-- Breadcrumb & Search -->
            <div class="explorer-header">
                <div class="breadcrumb">
                    <i class="ph ph-cabinet"></i>
                    <span @click="selectedMonth = null" style="cursor: pointer;" title="Back to Overview">Digital Archive</span>
                    <i class="ph ph-caret-right"></i>
                    <span x-text="'FY ' + currentYear"></span>
                    <template x-if="selectedMonth">
                        <div style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i class="ph ph-caret-right"></i>
                            <span x-text="selectedMonth"></span>
                        </div>
                    </template>
                </div>
                
                <div class="action-bar">
                    <div class="search-box">
                        <i class="ph ph-magnifying-glass"></i>
                        <input type="text" x-model="search" placeholder="Filter files or cases...">
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="explorer-grid">
                
                <!-- 1. MONTH FOLDERS VIEW (Overview for Current Year) -->
                <div x-show="!selectedMonth" class="grid-layout">
                    @foreach($archive as $year => $months)
                        <div x-show="currentYear === '{{ $year }}'" class="year-view">
                            @foreach($months as $month => $data)
                                <div class="month-card" @click="selectedMonth = '{{ \Carbon\Carbon::create(null, $month, 1)->format('F') }}'">
                                    <div class="month-icon">
                                        <i class="ph-fill ph-folder" style="color: #f59e0b;"></i>
                                        <div class="batch-count">{{ count($data['reports'] ?? []) + count($data['cases'] ?? []) }}</div>
                                    </div>
                                    <div class="month-label">{{ \Carbon\Carbon::create(null, $month, 1)->format('F') }}</div>
                                    <div class="month-meta">
                                        {{ count($data['reports'] ?? []) }} Reports · {{ count($data['cases'] ?? []) }} Cases
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    
                    @if(empty($archive))
                        <div class="empty-explorer">
                            <i class="ph ph-mask-sad"></i>
                            <h3>No archives available for this year.</h3>
                            <p>Generate reports or settle cases to populate the cabinet.</p>
                        </div>
                    @endif
                </div>

                <!-- 2. DETAILED MONTH VIEW (When a folder is opened) -->
                <template x-if="selectedMonth">
                    <div class="folder-content">
                        <button @click="selectedMonth = null" class="btn-back">
                            <i class="ph ph-arrow-left"></i> Back to FY <span x-text="currentYear"></span>
                        </button>

                        <div class="content-sections">
                            <!-- Reports Section -->
                            <section class="archive-section">
                                <div class="section-header">
                                    <h4><i class="ph ph-files"></i> Management Reports</h4>
                                    <p>Official transmittal records and endorsement letters.</p>
                                </div>
                                <div class="doc-list">
                                    @foreach($archive as $year => $months)
                                        @foreach($months as $month => $data)
                                            <div x-show="currentYear === '{{ $year }}' && selectedMonth === '{{ \Carbon\Carbon::create(null, $month, 1)->format('F') }}'">
                                                @forelse($data['reports'] ?? [] as $report)
                                                    <div class="doc-item" x-show="'{{ strtolower($report->type) }}'.includes(search.toLowerCase())">
                                                        <div class="doc-info">
                                                            <div class="doc-type-icon">
                                                                <i class="ph ph-file-pdf"></i>
                                                            </div>
                                                            <div>
                                                                <span class="doc-title">{{ $report->type == 'kp-form-28' ? 'KP Form 28 (Transmittal)' : ($report->type == 'endorsement' ? 'Endorsement Letter' : $report->type) }}</span>
                                                                <small class="doc-meta">Archived {{ $report->updated_at->format('M d, Y') }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="doc-actions">
                                                            <a href="{{ route('reports.show', ['type' => Str::slug($report->type), 'month' => $report->month, 'year' => $report->year]) }}" class="action-link" title="View Document"><i class="ph ph-eye"></i></a>
                                                            
                                                            <!-- Added Print Action -->
                                                            <a href="{{ route('reports.print', $report->id) }}?print=1" class="action-link highlight" title="Print Official Report"><i class="ph ph-printer"></i></a>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="empty-inline">No reports archived for this month.</div>
                                                @endforelse
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </section>

                            <!-- Cases Section -->
                            <section class="archive-section">
                                <div class="section-header">
                                    <h4><i class="ph ph-scales"></i> Settled Case Records</h4>
                                    <p>Physical evidence of dispute resolutions finalized this month.</p>
                                </div>
                                <div class="doc-list">
                                    @foreach($archive as $year => $months)
                                        @foreach($months as $month => $data)
                                            <div x-show="currentYear === '{{ $year }}' && selectedMonth === '{{ \Carbon\Carbon::create(null, $month, 1)->format('F') }}'">
                                                @forelse($data['cases'] ?? [] as $case)
                                                    <div class="case-archive-item" x-show="'{{ strtolower($case->case_number) }}'.includes(search.toLowerCase()) || '{{ strtolower($case->complainant) }}'.includes(search.toLowerCase())">
                                                        <div class="case-header">
                                                            <div class="case-meta-info">
                                                                <span class="case-no">{{ $case->case_number }}</span>
                                                                <span class="case-participants">{{ $case->complainant }} vs {{ $case->respondent }}</span>
                                                            </div>
                                                            <div class="case-settlement-date">
                                                                @if($case->status === 'settled' && $case->settled_at)
                                                                    Settled: {{ $case->settled_at->format('M d, Y') }}
                                                                @else
                                                                    Certified: {{ $case->updated_at->format('M d, Y') }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="case-details">
                                                            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0.75rem;">
                                                                <div class="hearing-trail">
                                                                    <i class="ph ph-clock-counter-clockwise"></i>
                                                                    <span>{{ $case->hearings->count() }} Hearings Recorded</span>
                                                                </div>
                                                                
                                                                @if($case->pangkats->isNotEmpty())
                                                                    @php $pangkat = $case->pangkats->first(); @endphp
                                                                    <div class="hearing-trail" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                                                        <i class="ph ph-users-three"></i>
                                                                        <span title="Pangkat Team">
                                                                            {{ $pangkat->chairperson->name ?? 'N/A' }} (Chair)
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="case-actions">
                                                                <a href="{{ route('cases.show', $case) }}" class="btn-view-case">
                                                                    Open Full Case Folder <i class="ph ph-arrow-right"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="empty-inline">No cases recorded for this month.</div>
                                                @endforelse
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </section>
                        </div>
                    </div>
                </template>

            </div>
        </main>
    </div>
</div>

<style>
    .explorer-wrapper {
        height: calc(100vh - 120px);
        margin: 1rem;
        background: var(--bg-card);
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .explorer-container {
        display: flex;
        height: 100%;
    }

    /* Sidebar Navigation */
    .folder-sidebar {
        width: 300px;
        background: var(--bg-hover);
        border-right: 1px solid var(--border-color);
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }

    .sidebar-section h3 {
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }

    .folder-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 16px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.9375rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 0.5rem;
    }

    .folder-item i {
        font-size: 1.5rem;
        color: #94a3b8;
    }

    .folder-item:hover {
        background: var(--bg-hover);
        transform: translateX(5px);
    }

    .folder-item.active {
        background: var(--bg-page);
        color: var(--text-primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .folder-item.active i {
        color: #f59e0b;
    }

    .ref-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        color: #6366f1;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.875rem;
        transition: opacity 0.2s;
    }

    .ref-link:hover { opacity: 0.8; }

    .cabinet-stats {
        background: var(--bg-card);
        padding: 1.25rem;
        border-radius: 16px;
        border: 1px solid var(--border-light);
    }

    .cabinet-stats p { margin: 0; font-size: 0.8125rem; color: var(--text-primary); }

    /* Main Area */
    .explorer-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: var(--bg-card);
    }

    .explorer-header {
        padding: 1.5rem 3rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1rem;
        color: #64748b;
        font-weight: 700;
    }

    .search-box {
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        padding: 0.625rem 1.5rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .search-box input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 0.875rem;
        width: 240px;
        color: var(--text-primary);
        font-weight: 500;
    }

    /* Grid Layout - Months */
    .explorer-grid {
        flex: 1;
        padding: 3rem;
        overflow-y: auto;
    }

    .grid-layout {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 2.5rem;
    }

    .year-view {
        display: contents;
    }
        gap: 2.5rem;
    }

    .month-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .month-card:hover { transform: scale(1.05); }

    .month-icon {
        position: relative;
        font-size: 5rem;
        line-height: 1;
        margin-bottom: 1rem;
    }

    .batch-count {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: #6366f1;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    .month-label { font-weight: 800; color: var(--text-primary); font-size: 1.125rem; }
    .month-meta { font-size: 0.75rem; color: #94a3b8; font-weight: 600; margin-top: 0.25rem; }

    /* Folder Content View */
    .folder-content {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .btn-back {
        background: var(--bg-page);
        border: 1px solid var(--border-color);
        padding: 0.625rem 1.25rem;
        border-radius: 12px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2.5rem;
    }

    .btn-back:hover { background: var(--bg-hover); color: var(--text-primary); }

    .content-sections {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 3rem;
    }

    .archive-section .section-header { margin-bottom: 1.5rem; }
    .archive-section h4 { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.75rem; }
    .archive-section p { font-size: 0.875rem; color: #64748b; margin: 0; }

    .doc-list { display: flex; flex-direction: column; gap: 1rem; }

    .doc-item {
        background: var(--bg-page);
        border: 1px solid var(--border-light);
        padding: 1.25rem;
        border-radius: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        transition: border-color 0.2s;
    }

    .doc-item:hover { border-color: #6366f1; }

    .doc-info { display: flex; align-items: center; gap: 1rem; }
    .doc-type-icon { width: 44px; height: 44px; border-radius: 12px; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .doc-title { display: block; font-weight: 700; color: var(--text-primary); font-size: 0.9375rem; }
    .doc-meta { color: #94a3b8; font-size: 0.75rem; }

    .doc-actions { display: flex; gap: 0.75rem; }
    .action-link { width: 36px; height: 36px; border-radius: 10px; background: var(--bg-page); color: #64748b; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s; }
    .action-link:hover { background: var(--bg-hover); color: var(--text-primary); }
    .action-link.highlight { background: #eff6ff; color: #3b82f6; }
    .action-link.highlight:hover { background: #dbeafe; }

    /* Case Items */
    .case-archive-item {
        background: var(--bg-page);
        border-radius: 20px;
        padding: 1.5rem;
        border-left: 4px solid #10b981;
    }

    .case-header { display: flex; justify-content: space-between; margin-bottom: 1.25rem; align-items: flex-start; }
    .case-no { display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #10b981; letter-spacing: 0.05em; }
    .case-participants { display: block; font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem; }
    .case-settlement-date { font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); background: var(--bg-card); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border-light); }

    .case-details { display: flex; justify-content: space-between; align-items: center; }
    .hearing-trail { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748b; font-weight: 600; }
    .btn-view-case { text-decoration: none; color: #10b981; font-weight: 800; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; transition: transform 0.2s; }
    .btn-view-case:hover { transform: translateX(5px); }

    .empty-inline { padding: 2rem; text-align: center; color: #94a3b8; font-style: italic; background: var(--bg-page); border-radius: 16px; border: 2px dashed var(--border-color); font-size: 0.875rem; }

    .empty-explorer { grid-column: 1 / -1; padding: 6rem; text-align: center; color: #94a3b8; }
    .empty-explorer i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.3; }
    .empty-explorer h3 { color: var(--text-secondary); font-weight: 800; margin-bottom: 0.5rem; }
</style>
@endsection

