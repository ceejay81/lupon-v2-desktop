@extends('layouts.app')

@section('title', 'Digital Archives | Lupon')
@section('page-title', 'Digital Archives')

@section('content')

<!-- Alpine App Root -->
<div class="archive-app" 
     x-data="archiveApp()" 
     @keydown.window.prevent.slash="$refs.searchInput.focus()"
     @keydown.window.j="nextMonth()"
     @keydown.window.k="prevMonth()">

    <!-- Global Toolbar -->
    <div class="toolbar-sticky">
        <div class="toolbar-inner">
            <div class="search-container">
                <i class="ph ph-magnifying-glass"></i>
                <!-- Debounced search input -->
                <input type="text" 
                       x-ref="searchInput"
                       x-model.debounce.300ms="searchQuery" 
                       placeholder="Filter by year, month, type, case number, or party name... (Press '/' to focus)">
                
                <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="clear-search">
                    <i class="ph ph-x"></i>
                </button>
            </div>

            <div style="display:flex; align-items:center; gap: 1rem;">
                <!-- Desktop Only Quick Jump -->
                <div class="quick-jump desktop-only" x-show="availableMonths.length > 0">
                    <select x-model="quickJumpTarget" @change="jumpToMonth()" style="padding: 0.5rem 2rem 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-hover); color: var(--text-primary); font-size: 0.85rem; font-weight: 500;">
                        <option value="">Quick Jump...</option>
                        <template x-for="m in availableMonths" :key="m.id">
                            <option :value="m.id" x-text="m.label"></option>
                        </template>
                    </select>
                </div>

                <!-- View Mode Toggle -->
                <div class="view-toggles">
                    <button @click="viewMode = 'detailed'" :class="viewMode === 'detailed' ? 'active' : ''" title="Detailed Card View">
                        <i class="ph ph-squares-four"></i>
                    </button>
                    <button @click="viewMode = 'compact'" :class="viewMode === 'compact' ? 'active' : ''" title="Compact List View">
                        <i class="ph ph-list-dashes"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Filter Chips Area (if active search) -->
        <div x-show="searchQuery" class="filter-chips">
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Active Filters:</span>
            <div class="chip">
                <span x-text="'Search: ' + searchQuery"></span>
                <i class="ph ph-x" @click="searchQuery = ''" style="cursor: pointer;"></i>
            </div>
        </div>
    </div>

    <!-- Main Scrolling Area -->
    <main class="archive-main">
        
        <template x-if="Object.keys(archiveData).length === 0 && !loadingOlder">
            <div class="empty-state-global">
                <i class="ph-fill ph-folder-open"></i>
                <h3>Your archive is empty</h3>
                <p>There are no finalized reports or settled cases. Head over to the Cases or Reports module to get started.</p>
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: center;">
                    <a href="{{ route('reports.index') }}" class="btn btn-primary" style="text-decoration: none;">Upload Reports</a>
                    <a href="{{ route('cases.index') }}" class="btn btn-secondary" style="text-decoration: none;">Manage Cases</a>
                </div>
            </div>
        </template>

        <template x-for="(months, year) in filteredArchive" :key="year">
            <div class="year-accordion" x-data="{ expanded: true }">
                
                <!-- Year Header -->
                <div class="year-header" @click="expanded = !expanded">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <i class="ph ph-caret-down accordion-chevron" :style="expanded ? '' : 'transform: rotate(-90deg)'"></i>
                        <h2 x-text="'FY ' + year"></h2>
                        <span class="year-badge" x-text="calculateYearTotal(months) + ' Items'"></span>
                    </div>
                </div>

                <!-- Year Content (Months) -->
                <div x-show="expanded" x-collapse>
                    <div class="months-container">
                        <template x-for="(data, monthNum) in months" :key="monthNum">
                            <div class="month-card" :id="'month-' + year + '-' + monthNum" x-data="{ monthExpanded: false }">
                                
                                <div class="month-card-header" @click="monthExpanded = !monthExpanded" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='var(--bg-card)'" onmouseout="this.style.background='var(--bg-hover)'">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <i class="ph ph-caret-right accordion-chevron" :style="monthExpanded || searchQuery ? 'transform: rotate(90deg)' : ''" style="transition: transform 0.2s; font-size: 1.1rem;"></i>
                                        <div class="month-icon"><i class="ph-fill ph-calendar-check"></i></div>
                                        <div>
                                            <h3 class="month-title" x-text="getMonthName(monthNum) + ' ' + year"></h3>
                                        </div>
                                    </div>
                                    <div class="month-pill" x-text="calculateMonthTotal(data) + ' Total'"></div>
                                </div>
                                
                                <div x-show="monthExpanded || searchQuery" x-collapse>
                                    <template x-if="monthExpanded || searchQuery">
                                        <div class="month-card-body">
                                    
                                    <!-- Reports Section -->
                                    <template x-if="data.reports && data.reports.length > 0">
                                        <div class="section-block">
                                            <h4 class="section-title"><i class="ph ph-files"></i> Finalized Reports</h4>
                                            <div :class="viewMode === 'compact' ? 'items-list compact' : 'items-grid'">
                                                <template x-for="report in data.reports" :key="'r-'+report.id">
                                                    
                                                    <!-- Report Item Row -->
                                                    <div class="item-row">
                                                        <div class="item-left">
                                                            <div class="item-icon" :class="getFileIconColor(report.file_path)">
                                                                <i class="ph-fill" :class="getFileIcon(report.file_path)"></i>
                                                            </div>
                                                            <div class="item-details">
                                                                <span class="item-title" x-text="report.type"></span>
                                                                
                                                                <!-- Detailed metadata -->
                                                                <div class="item-meta" x-show="viewMode === 'detailed'">
                                                                    <template x-if="report.file_path">
                                                                        <span>
                                                                            <i class="ph ph-paperclip"></i> <span x-text="getFilename(report.file_path)"></span>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="!report.file_path">
                                                                        <span class="badge-legacy"><i class="ph ph-clock-counter-clockwise"></i> Legacy V1</span>
                                                                    </template>
                                                                    <span class="dot-separator">•</span>
                                                                    <span x-text="formatDate(report.updated_at)"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="item-actions">
                                                            <template x-if="report.file_path">
                                                                <div style="display: flex; gap: 0.35rem;">
                                                                    <template x-if="isPdf(report.file_path)">
                                                                        <a :href="'/reports/' + report.id + '/view-pdf'" class="btn-icon" title="View PDF Document">
                                                                            <i class="ph ph-file-pdf"></i>
                                                                        </a>
                                                                    </template>
                                                                    <template x-if="!isPdf(report.file_path)">
                                                                        <button @click="openExternal('/reports/' + report.id + '/open')" class="btn-icon" title="Open locally">
                                                                            <i class="ph ph-arrow-square-out"></i>
                                                                        </button>
                                                                    </template>
                                                                    <a :href="'/reports/' + report.id + '/download'" class="btn-icon highlight" title="Download">
                                                                        <i class="ph ph-download-simple"></i>
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>

                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Cases Section -->
                                    <template x-if="data.cases && data.cases.length > 0">
                                        <div class="section-block">
                                            <h4 class="section-title" style="margin-top: 1.5rem;"><i class="ph ph-scales"></i> Settled Case Records</h4>
                                            <div :class="viewMode === 'compact' ? 'items-list compact' : 'items-grid'">
                                                <template x-for="lcase in data.cases" :key="'c-'+lcase.id">
                                                    
                                                    <!-- Case Item Row -->
                                                    <div class="item-row case-row border-accent">
                                                        <div class="item-left">
                                                            <div class="item-icon icon-case">
                                                                <i class="ph-fill ph-folder-open"></i>
                                                            </div>
                                                            <div class="item-details">
                                                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                                    <span class="item-title" x-text="lcase.case_number"></span>
                                                                    <span class="badge" :class="lcase.status === 'settled' ? 'badge-success' : 'badge-warning'" x-text="formatStatus(lcase.status)"></span>
                                                                </div>
                                                                
                                                                <!-- Detailed metadata -->
                                                                <div class="item-meta" x-show="viewMode === 'detailed'">
                                                                    <span class="parties" x-text="lcase.complainant + ' vs ' + lcase.respondent"></span>
                                                                    <span class="dot-separator">•</span>
                                                                    <span x-text="lcase.status === 'settled' ? 'Settled ' + formatDate(lcase.settled_at) : 'Certified ' + formatDate(lcase.updated_at)"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="item-actions">
                                                            <a :href="'/cases/' + lcase.id" class="btn-link">View Folder <i class="ph ph-arrow-right"></i></a>
                                                        </div>
                                                    </div>

                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Empty Month Fallback (Only happens if search wipes it out but parent still exists) -->
                                    <template x-if="(!data.reports || data.reports.length === 0) && (!data.cases || data.cases.length === 0)">
                                         <div class="empty-month">
                                             No records match your search criteria in this month.
                                         </div>
                                    </template>

                                        </div> <!-- end body -->
                                    </template>
                                </div> <!-- end collapse tracker -->
                            </div> <!-- end card -->
                        </template>
                    </div>
                </div> <!-- end collapse -->
                
            </div>
        </template>

        <!-- No Search Results -->
        <template x-if="Object.keys(filteredArchive).length === 0 && searchQuery && !loadingOlder">
            <div style="text-align: center; padding: 4rem; color: var(--text-muted);">
                <i class="ph ph-magnifying-glass" style="font-size: 3rem; opacity: 0.5;"></i>
                <h3 style="margin-top: 1rem; color: var(--text-primary);">No matches found</h3>
                <p>Try adjusting your search filters.</p>
            </div>
        </template>

        <!-- Progressive Loading Trigger -->
        <template x-if="hasOlderCases">
            <div style="text-align: center; padding: 2rem 0 4rem; margin-top: 1rem;">
                <button @click="loadOlderArchives()" class="btn btn-secondary" :disabled="loadingOlder" style="border-radius: 9999px; padding: 0.75rem 2rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <i class="ph" :class="loadingOlder ? 'ph-spinner ph-spin' : 'ph-clock-counter-clockwise'"></i>
                    <span x-text="loadingOlder ? 'Loading Older Archives...' : 'Load Older Archives'"></span>
                </button>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.75rem;">Showing records from the last 24 months for performance.</div>
            </div>
        </template>

    </main>
</div>

<!-- Scripts - Alpine, plugins, and Phosphor Icons are loaded via Vite in layout -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('archiveApp', () => ({
            // State
            searchQuery: '',
            viewMode: Alpine.$persist('detailed').as('archive_view_mode'), // detailed or compact
            hasOlderCases: @json($hasOlderCases ?? false),
            loadingOlder: false,
            
            // Raw JSON Data payload from Controller
            archiveData: @json($archive ?? []),
            
            // Quick Jump handling
            quickJumpTarget: '',
            
            get availableMonths() {
                let list = [];
                for (const year in this.archiveData) {
                    for (const monthNum in this.archiveData[year]) {
                        list.push({
                            id: `month-${year}-${monthNum}`,
                            label: `${this.getMonthName(monthNum)} ${year}`,
                            y: year,
                            m: monthNum
                        });
                    }
                }
                // Sort descending usually handled by Object keys insertion order but let's be safe
                return list;
            },

            jumpToMonth() {
                if(!this.quickJumpTarget) return;
                const el = document.getElementById(this.quickJumpTarget);
                if(el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Highlight effect
                    el.style.transition = 'box-shadow 0.3s';
                    el.style.boxShadow = '0 0 0 4px rgba(99, 102, 241, 0.4)';
                    setTimeout(() => el.style.boxShadow = '', 2000);
                }
                this.quickJumpTarget = '';
            },

            // Filtering Logic
            get filteredArchive() {
                if (this.searchQuery.trim() === '') return this.archiveData;
                
                const q = this.searchQuery.toLowerCase();
                const filtered = {};

                for (const [year, months] of Object.entries(this.archiveData)) {
                    let yearHasMatch = false;
                    const filteredMonths = {};
                    
                    // Match against Year
                    if (year.includes(q)) yearHasMatch = true;

                    for (const [monthNum, data] of Object.entries(months)) {
                        const monthName = this.getMonthName(monthNum).toLowerCase();
                        let monthHasMatch = yearHasMatch || monthName.includes(q);
                        
                        let fReports = [];
                        let fCases = [];

                        if (data.reports) {
                            fReports = data.reports.filter(r => {
                                return monthHasMatch || 
                                       (r.type && r.type.toLowerCase().includes(q)) || 
                                       (r.file_path && r.file_path.toLowerCase().includes(q));
                            });
                        }

                        if (data.cases) {
                            fCases = data.cases.filter(c => {
                                return monthHasMatch || 
                                       (c.case_number && c.case_number.toLowerCase().includes(q)) ||
                                       (c.complainant && c.complainant.toLowerCase().includes(q)) ||
                                       (c.respondent && c.respondent.toLowerCase().includes(q)) ||
                                       (c.status && c.status.toLowerCase().includes(q));
                            });
                        }

                        if (fReports.length > 0 || fCases.length > 0 || monthHasMatch) {
                            // If month matches string directly but files don't, return all files in month
                            filteredMonths[monthNum] = {
                                reports: monthHasMatch ? (data.reports || []) : fReports,
                                cases: monthHasMatch ? (data.cases || []) : fCases
                            };
                        }
                    }

                    if (Object.keys(filteredMonths).length > 0) {
                        filtered[year] = filteredMonths;
                    }
                }

                // Sort keys descending to maintain structure
                const sortedObj = {};
                Object.keys(filtered).sort((a,b)=>b-a).forEach(k => {
                    sortedObj[k] = {};
                    Object.keys(filtered[k]).sort((a,b)=>b-a).forEach(m => {
                        sortedObj[k][m] = filtered[k][m];
                    });
                });

                return sortedObj;
            },

            // Helpers
            getMonthName(num) {
                const date = new Date();
                date.setMonth(parseInt(num) - 1);
                return date.toLocaleString('en-US', { month: 'long' });
            },

            calculateYearTotal(months) {
                let total = 0;
                for (const m in months) {
                    total += this.calculateMonthTotal(months[m]);
                }
                return total;
            },

            calculateMonthTotal(data) {
                return (data.reports ? data.reports.length : 0) + (data.cases ? data.cases.length : 0);
            },

            formatDate(datetimeString) {
                if(!datetimeString) return '';
                const d = new Date(datetimeString);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            },

            formatStatus(status) {
                if(!status) return '';
                return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            },

            getFilename(path) {
                if(!path) return '';
                return path.split(/[\\/]/).pop();
            },

            isPdf(path) {
                if(!path) return false;
                return path.toLowerCase().endsWith('.pdf');
            },

            getFileIcon(path) {
                if(!path) return 'ph-file';
                const lower = path.toLowerCase();
                if(lower.endsWith('.pdf')) return 'ph-file-pdf';
                if(lower.endsWith('.docx') || lower.endsWith('.doc')) return 'ph-file-doc';
                if(lower.endsWith('.xlsx') || lower.endsWith('.xls')) return 'ph-file-xls';
                if(lower.endsWith('.jpg') || lower.endsWith('.png')) return 'ph-image';
                return 'ph-file-text';
            },

            getFileIconColor(path) {
                if(!path) return 'color-legacy';
                const lower = path.toLowerCase();
                if(lower.endsWith('.pdf')) return 'color-pdf';
                if(lower.endsWith('.docx') || lower.endsWith('.doc')) return 'color-doc';
                if(lower.endsWith('.xlsx') || lower.endsWith('.xls')) return 'color-xls';
                if(lower.endsWith('.jpg') || lower.endsWith('.png')) return 'color-img';
                return 'color-other';
            },

            openExternal(url) {
                // Same logic preservation from V1
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        if (data.path && window.electron && window.electron.openFile) {
                            window.electron.openFile(data.path);
                        } else {
                            // Fallback to download attempt if error/legacy
                            window.location.href = url.replace('/open', '/download');
                        }
                    })
                    .catch(() => window.location.href = url.replace('/open', '/download'));
            },

            async loadOlderArchives() {
                this.loadingOlder = true;
                try {
                    const response = await fetch('{{ route('folderized-reports.older') }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const additionalArchive = await response.json();
                    
                    // Merge logic
                    for (const year in additionalArchive) {
                        if (!this.archiveData[year]) {
                            this.archiveData[year] = {};
                        }
                        for (const month in additionalArchive[year]) {
                            if (!this.archiveData[year][month]) {
                                this.archiveData[year][month] = { reports: [], cases: [] };
                            }
                            // Append new cases avoiding duplicates (though API slices at 24mo explicitly)
                            if (additionalArchive[year][month].cases) {
                                this.archiveData[year][month].cases.push(...additionalArchive[year][month].cases);
                            }
                        }
                    }
                    this.hasOlderCases = false; // Hide button after fetch
                } catch(e) {
                    console.error("Failed fetching older archives", e);
                    alert("Failed connection while loading older cases.");
                }
                this.loadingOlder = false;
            },

            // Hotkeys (J/K)
            nextMonth() {
                // Not heavily reliant for core UI, mostly QoL
                if(document.activeElement && document.activeElement.tagName === 'INPUT') return;
                const cards = document.querySelectorAll('.month-card');
                let found = false;
                for(let i=0; i<cards.length; i++) {
                    const rect = cards[i].getBoundingClientRect();
                    // Find first element below viewport center
                    if(rect.top > (window.innerHeight/3)) {
                        cards[i].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        found = true;
                        break;
                    }
                }
            },
            
            prevMonth() {
                if(document.activeElement && document.activeElement.tagName === 'INPUT') return;
                const cards = document.querySelectorAll('.month-card');
                for(let i=cards.length-1; i>=0; i--) {
                    const rect = cards[i].getBoundingClientRect();
                    // Find first element above viewport top
                    if(rect.bottom < (window.innerHeight/3)) {
                        cards[i].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        break;
                    }
                }
            }

        }));
    });
</script>

<!-- Styles -->
<style>
    /* CSS Custom Properties mapping to established brand styles */
    :root {
        --archive-bg: #f8fafc;
        --archive-card: #ffffff;
        --accent-blue: #6366f1;
        --border-soft: #e2e8f0;
        --text-strong: #0f172a;
        --text-sub: #64748b;
    }
    html[data-theme="dark"] {
        --archive-bg: #0f172a;
        --archive-card: #1e293b;
        --border-soft: #334155;
        --text-strong: #f8fafc;
        --text-sub: #94a3b8;
    }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: var(--bg-body);
    }

    .archive-app {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 60px);
        overflow: hidden;
    }

    /* Toolbar */
    .toolbar-sticky {
        background: var(--bg-card);
        border-bottom: 1px solid var(--border-color);
        z-index: 40;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    
    .toolbar-inner {
        padding: 1.25rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }

    .search-container {
        flex: 1;
        max-width: 600px;
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-container i.ph-magnifying-glass {
        position: absolute;
        left: 1.25rem;
        color: var(--text-muted);
        font-size: 1.25rem;
    }

    .search-container input {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 3rem;
        border-radius: 12px;
        border: 2px solid var(--border-color);
        background: var(--bg-hover);
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .search-container input:focus {
        border-color: var(--primary);
        outline: none;
        background: var(--bg-card);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .clear-search {
        position: absolute;
        right: 1rem;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        border-radius: 50%;
        padding: 4px;
    }
    .clear-search:hover { background: var(--border-color); color: var(--text-primary); }

    .view-toggles {
        display: flex;
        background: var(--bg-hover);
        padding: 0.25rem;
        border-radius: 10px;
        border: 1px solid var(--border-color);
    }

    .view-toggles button {
        background: transparent;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        color: var(--text-muted);
        font-size: 1.35rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .view-toggles button:hover { color: var(--text-primary); }
    .view-toggles button.active {
        background: var(--bg-card);
        color: var(--primary);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .filter-chips {
        padding: 0 2rem 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    /* Main Scrolling Container */
    .archive-main {
        flex: 1;
        overflow-y: auto;
        padding: 2rem 3rem;
        scroll-behavior: smooth;
    }

    /* Year Accordions */
    .year-accordion {
        margin-bottom: 2rem;
    }

    .year-header {
        position: sticky;
        top: -2.1rem; /* Clings slightly to top inside scroll */
        z-index: 20;
        background: rgba(var(--bg-card-rgb, 255,255,255), 0.95);
        backdrop-filter: blur(8px);
        padding: 1rem 1.5rem;
        border-radius: 16px;
        cursor: pointer;
        margin-bottom: 1rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        transition: all 0.2s;
        /* Defaulting fallback for darkmode variable extraction */
        background: var(--bg-card);
    }
    .year-header:hover { border-color: var(--primary); }

    .accordion-chevron {
        font-size: 1.25rem;
        color: var(--primary);
        transition: transform 0.3s;
    }

    .year-header h2 {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .year-badge {
        background: var(--bg-hover);
        color: var(--text-secondary);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }

    /* Month Cards */
    .months-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        padding-left: 1rem;
        border-left: 2px solid var(--border-color);
        margin-left: 2rem;
        padding-bottom: 1rem;
    }

    .month-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-left: 4px solid var(--primary);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .month-card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--bg-hover);
    }

    .month-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--bg-card);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .month-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .month-pill {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.35rem 1rem;
        border-radius: 9999px;
    }

    .month-card-body {
        padding: 1.75rem;
    }

    .section-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Grid & List Structural Classes */
    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1rem;
    }

    .items-list.compact {
        display: flex;
        flex-direction: column;
    }

    .item-row {
        background: var(--bg-hover);
        border: 1px solid var(--border-light);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        transition: all 0.2s;
    }
    .item-row:hover { border-color: var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.03); }

    .items-list.compact .item-row {
        border-radius: 0;
        border: none;
        border-bottom: 1px solid var(--border-light);
        padding: 0.75rem 1rem;
        background: transparent;
    }
    .items-list.compact .item-row:last-child { border-bottom: none; }
    .items-list.compact .item-row:hover { background: var(--bg-hover); }

    .item-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        overflow: hidden;
    }

    .item-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    /* Colors */
    .color-pdf { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .color-doc { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .color-xls { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .color-img { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .color-other { background: rgba(100, 116, 139, 0.1); color: #64748b; }
    .color-legacy { background: rgba(245, 158, 11, 0.1); color: #d97706; }
    .icon-case { background: rgba(16, 185, 129, 0.1); color: #10b981; }

    .item-details { display: flex; flex-direction: column; overflow: hidden; }
    
    .item-title {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.95rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .items-list.compact .item-meta { display: none; } /* Hide in compact */

    .dot-separator { opacity: 0.5; }

    .item-actions { flex-shrink: 0; }

    .btn-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-icon:hover { border-color: var(--primary); color: var(--primary); }
    .btn-icon.highlight { color: var(--accent-blue); border-color: rgba(99, 102, 241, 0.3); }
    .btn-icon.highlight:hover { background: rgba(99, 102, 241, 0.1); }

    .btn-link {
        font-size: 0.85rem;
        font-weight: 800;
        color: #10b981;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .btn-link:hover { text-decoration: underline; }

    .badge-legacy {
        background: #fffbeb; color: #d97706; border: 1px solid #fcd34d;
        padding: 2px 6px; border-radius: 4px; font-weight: 600;
    }

    html[data-theme="dark"] .badge-legacy {
        background: rgba(217, 119, 6, 0.2); border-color: rgba(217, 119, 6, 0.5);
    }

    .empty-state-global {
        text-align: center; padding: 6rem 2rem;
    }
    .empty-state-global i { font-size: 4rem; color: var(--border-color); margin-bottom: 1rem; }
    .empty-state-global p { color: var(--text-muted); }

    .empty-month {
        padding: 2rem;
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.875rem;
        font-style: italic;
    }

    /* Responsive Handling */
    @media (max-width: 768px) {
        .toolbar-inner { flex-direction: column; align-items: stretch; gap: 1rem; padding: 1rem; }
        .desktop-only { display: none !important; }
        .archive-main { padding: 1rem; }
        .months-container { margin-left: 0; padding-left: 0; border-left: none; }
        
        .item-row { flex-direction: column; align-items: stretch; }
        .item-actions { display: flex; justify-content: flex-end; padding-top: 0.75rem; border-top: 1px solid var(--border-color); margin-top: 0.5rem; }
        .item-left { align-items: flex-start; }
    }
</style>

@endsection
