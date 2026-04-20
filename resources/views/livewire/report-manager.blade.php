<div x-data="{
    isDragging: false,
    deleteConfirmId: null,
    deleteConfirmName: '',
    openFileViaElectron(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.path && window.electron && window.electron.openFile) {
                    window.electron.openFile(data.path);
                } else if (data.status === 'legacy') {
                    Livewire.dispatch('toast', { type: 'error', message: 'Legacy reports cannot be opened directly.' });
                } else {
                    window.location.href = url.replace('/open', '/download');
                }
            })
            .catch(() => window.location.href = url.replace('/open', '/download'));
    },
    confirmDelete(id, name) {
        this.deleteConfirmId = id;
        this.deleteConfirmName = name;
    }
}">

    {{-- ─── Delete Confirmation Modal ─── --}}
    <template x-if="deleteConfirmId !== null">
        <div style="position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(6px);z-index:9000;display:flex;align-items:center;justify-content:center;"
             @keydown.escape.window="deleteConfirmId=null">
            <div style="background:var(--bg-card);border-radius:1rem;box-shadow:0 24px 48px -8px rgba(0,0,0,0.35);width:100%;max-width:440px;overflow:hidden;">
                <div style="padding:2rem;">
                    <div style="width:48px;height:48px;background:#fef2f2;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                        <i class="ph ph-trash" style="font-size:1.5rem;color:#ef4444;"></i>
                    </div>
                    <p style="font-size:1rem;font-weight:800;color:var(--text-primary);margin-bottom:0.4rem;">Delete Report?</p>
                    <p style="font-size:0.875rem;color:var(--text-secondary);margin-bottom:0.25rem;" x-text="`&quot;${deleteConfirmName}&quot;`"></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">This action cannot be undone. The file will be permanently removed from the server.</p>
                </div>
                <div style="padding:0 2rem 1.75rem;display:flex;gap:0.75rem;justify-content:flex-end;">
                    <button @click="deleteConfirmId=null" type="button" class="btn-sm" style="height:38px;padding:0 1.25rem;">Cancel</button>
                    <button @click="$wire.delete(deleteConfirmId); deleteConfirmId=null" type="button"
                        style="height:38px;padding:0 1.25rem;background:#ef4444;color:white;border:none;border-radius:var(--radius-sm);font-size:0.8rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;">
                        <i class="ph ph-trash"></i> Delete Report
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ─── Duplicate File Modal ─── --}}
    @if($showDuplicateModal && $duplicateInfo && $pendingFileInfo)
        <div style="position:fixed;inset:0;background:rgba(15,23,42,0.65);backdrop-filter:blur(8px);z-index:9000;display:flex;align-items:center;justify-content:center;padding:1rem;">
            <div style="background:var(--bg-card);border-radius:1.25rem;box-shadow:0 32px 64px -12px rgba(0,0,0,0.4);width:100%;max-width:500px;overflow:hidden;">
                <div style="padding:1.75rem 2rem 1.25rem;">
                    <div style="width:48px;height:48px;background:rgba(245,158,11,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                        <i class="ph ph-warning" style="font-size:1.5rem;color:#f59e0b;"></i>
                    </div>
                    <p style="font-size:1rem;font-weight:800;color:var(--text-primary);margin-bottom:0.35rem;">File Already Exists</p>
                    <p style="font-size:0.82rem;color:var(--text-secondary);">
                        A file named <strong>{{ $duplicateInfo['filename'] }}</strong> already exists for this period. What would you like to do?
                    </p>
                    @if($duplicateInfo['is_legacy'])
                        <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.75rem;padding:0.625rem 0.875rem;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:var(--radius-sm);">
                            <i class="ph ph-clock-counter-clockwise" style="color:#f59e0b;font-size:0.9rem;"></i>
                            <p style="font-size:0.75rem;color:#92400e;">This is a legacy V1 report. Replace is unavailable — use Save as New instead.</p>
                        </div>
                    @endif
                </div>

                {{-- Comparison Cards --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;padding:0 2rem 1.5rem;">
                    {{-- Existing --}}
                    <div style="padding:0.875rem;background:var(--bg-hover);border:1px solid var(--border-color);border-radius:var(--radius-md);">
                        <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);margin-bottom:0.5rem;">Existing File</p>
                        <p style="font-size:0.78rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;word-break:break-all;">{{ $duplicateInfo['filename'] }}</p>
                        <p style="font-size:0.72rem;color:var(--text-muted);">{{ $duplicateInfo['date'] }}</p>
                        <p style="font-size:0.72rem;color:var(--text-muted);">by {{ $duplicateInfo['uploader'] }}</p>
                    </div>
                    {{-- New --}}
                    <div style="padding:0.875rem;background:rgba(59,130,246,0.05);border:1px solid rgba(59,130,246,0.2);border-radius:var(--radius-md);">
                        <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#3b82f6;margin-bottom:0.5rem;">New File</p>
                        <p style="font-size:0.78rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;word-break:break-all;">{{ $pendingFileInfo['filename'] }}</p>
                        <p style="font-size:0.72rem;color:var(--text-muted);">{{ $pendingFileInfo['size'] }}</p>
                        <p style="font-size:0.72rem;color:var(--text-muted);">Ready to upload</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="padding:0 2rem 1.75rem;display:flex;gap:0.625rem;justify-content:flex-end;flex-wrap:wrap;">
                    <button wire:click="cancelDuplicateUpload" type="button" class="btn-sm" style="height:38px;padding:0 1.1rem;">
                        Cancel
                    </button>
                    <button wire:click="saveAsNewReport" type="button"
                        style="height:38px;padding:0 1.1rem;background:var(--bg-hover);border:1px solid var(--border-color);border-radius:var(--radius-sm);font-size:0.8rem;font-weight:700;color:var(--text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:0.4rem;transition:all 0.15s;"
                        onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                        onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">
                        <i class="ph ph-copy"></i> Save as New
                    </button>
                    @if(!$duplicateInfo['is_legacy'])
                        <button wire:click="replaceReport" type="button"
                            style="height:38px;padding:0 1.1rem;background:#ef4444;color:white;border:none;border-radius:var(--radius-sm);font-size:0.8rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.4rem;">
                            <i class="ph ph-arrows-merge"></i> Replace
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Info Callout ─── --}}
    <div style="display:flex;align-items:flex-start;gap:1rem;padding:1rem 1.25rem;background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);border-radius:var(--radius-md);margin-bottom:1.75rem;">
        <i class="ph ph-info" style="font-size:1.25rem;color:#3b82f6;flex-shrink:0;margin-top:0.1rem;"></i>
        <div style="flex:1;">
            <p style="font-size:0.85rem;font-weight:700;color:var(--text-primary);margin-bottom:0.2rem;">Monthly & Compliance Reports</p>
            <p style="font-size:0.8rem;color:var(--text-secondary);line-height:1.55;">This section is for generalized monthly reports such as DILG submissions and barangay performance summaries. Case-specific documents should be uploaded directly to the case folder.</p>
        </div>
        <a href="{{ route('cases.index') }}"
           style="flex-shrink:0;display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 1rem;background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius-sm);font-size:0.78rem;font-weight:700;color:var(--text-secondary);text-decoration:none;white-space:nowrap;transition:all 0.15s;"
           onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
           onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">
            <i class="ph ph-notebook"></i> Go to Cases
        </a>
    </div>

    {{-- ─── Upload / Replace Form ─── --}}
    <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius-lg);box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:2rem;overflow:hidden;">

        {{-- Replace Mode Banner --}}
        @if($isReplacing)
            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1.5rem;background:rgba(245,158,11,0.08);border-bottom:1px solid rgba(245,158,11,0.25);">
                <i class="ph ph-arrows-merge" style="font-size:1.1rem;color:#f59e0b;"></i>
                <p style="font-size:0.85rem;font-weight:700;color:#92400e;">
                    Replacing: <span style="font-weight:400;">{{ $replacingReportName }}</span>
                </p>
            </div>
        @endif

        <div style="padding:1.75rem;">
            <p style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);margin-bottom:1.5rem;">
                <i class="ph ph-upload-simple"></i>
                {{ $isReplacing ? 'Replace Report' : 'Upload New Report' }}
            </p>

            <form wire:key="report-upload-form" wire:submit.prevent="uploadReport" x-on:dragover.prevent="isDragging=true" x-on:dragleave.prevent="isDragging=false" x-on:drop.prevent="isDragging=false">

                {{-- Row 1: Month/Year + Type/Save As --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

                    {{-- Left: Month + Year --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.875rem;">
                        <div>
                            <label class="form-label">Month <span style="color:var(--danger);">*</span></label>
                            <select wire:model="month" class="form-control">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                @endfor
                            </select>
                            @error('month') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Year <span style="color:var(--danger);">*</span></label>
                            <select wire:model="year" class="form-control">
                                @for ($y = now()->year + 1; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                            @error('year') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Right: Report Type (full width) --}}
                    <div>
                        <label class="form-label">Report Type / Document Title <span style="color:var(--danger);">*</span></label>
                        <input wire:model="report_type"
                               type="text"
                               class="form-control"
                               placeholder="e.g. Monthly Transmittal Report January 2026"
                               autocomplete="off">
                        @error('report_type') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 2: Drop Zone --}}
                <div style="margin-bottom:1.25rem;">
                    <label class="form-label">File <span style="color:var(--danger);">*</span> <span style="color:var(--text-muted);font-weight:400;">— PDF, DOCX, XLSX, JPG, PNG · max 20 MB</span></label>
                    <label for="reportFile"
                           style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.5rem;padding:2rem;border:2px dashed var(--border-color);border-radius:var(--radius-md);background:var(--bg-hover);cursor:pointer;transition:all 0.2s;"
                           :style="isDragging ? 'border-color:var(--primary);background:rgba(99,102,241,0.05);' : ''"
                           x-on:dragover.prevent="isDragging=true"
                           x-on:dragleave.prevent="isDragging=false"
                           x-on:drop.prevent="isDragging=false">
                        <i class="ph ph-cloud-arrow-up" style="font-size:2rem;color:var(--primary);"></i>
                        
                        {{-- File Selection Loading State --}}
                        <div wire:loading wire:target="file" wire:key="loading-file" style="display: none; align-items: center; gap: 0.5rem; color: var(--primary); font-size: 0.85rem; font-weight: 700;">
                            <i class="ph ph-circle-notch ph-spin"></i> Processing file...
                        </div>

                        <div wire:loading.remove wire:target="file">
                            @if($file)
                                <p style="font-size:0.85rem;font-weight:700;color:var(--primary);">
                                    <i class="ph ph-file-check"></i> {{ $file->getClientOriginalName() }}
                                </p>
                            @else
                                <p style="font-size:0.85rem;font-weight:600;color:var(--text-secondary);">Drag and drop your file here, or click to browse</p>
                                <p style="font-size:0.75rem;color:var(--text-muted);">PDF · DOCX · XLSX · JPG · PNG · ODT · max 20 MB</p>
                            @endif
                        </div>
                    </label>
                    <input type="file" wire:model="file" id="reportFile" style="display:none;"
                           accept=".pdf,.doc,.docx,.odt,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp">
                    @error('file') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Row 3: Remarks --}}
                <div style="margin-bottom:1.5rem;">
                    <label class="form-label">Internal Notes / Remarks <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
                    <textarea wire:model="remarks"
                              class="form-control"
                              rows="2"
                              placeholder="Brief note about this report..."
                              style="resize:vertical;"></textarea>
                    @error('remarks') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Submit Row --}}
                <div style="display:flex;justify-content:flex-end;gap:0.75rem;">
                    @if($isReplacing)
                        <button type="button" wire:click="cancelReplace" class="btn-sm" style="height:40px;padding:0 1.25rem;">
                            Cancel
                        </button>
                    @endif
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="uploadReport"
                            class="btn btn-primary"
                            style="height:40px;padding:0 1.75rem;font-size:0.82rem;">
                        <span wire:loading.remove wire:target="uploadReport" wire:key="upload-btn-idle">
                            <i class="ph {{ $isReplacing ? 'ph-arrows-merge' : 'ph-upload-simple' }}"></i>
                            {{ $isReplacing ? 'Update Report' : 'Upload Report' }}
                        </span>
                        <span wire:loading wire:target="uploadReport" wire:key="upload-btn-loading" style="display: none; align-items: center; gap: 0.5rem;">
                            <i class="ph ph-circle-notch ph-spin"></i> Uploading...
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ─── Reports Table ─── --}}
    <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius-lg);box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

        {{-- Table Toolbar --}}
        <div style="display:flex;align-items:center;gap:1rem;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
            <p style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);flex-shrink:0;">
                <i class="ph ph-files"></i> Uploaded Reports
            </p>
            <div style="flex:1;position:relative;max-width:340px;">
                <i class="ph ph-magnifying-glass" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.9rem;pointer-events:none;"></i>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       class="form-control"
                       placeholder="Search by type or filename..."
                       style="padding-left:2.25rem;height:36px;font-size:0.82rem;">
            </div>
            <select wire:model.live="filter_year" class="form-control" style="width:auto;height:36px;font-size:0.82rem;padding:0 2rem 0 0.75rem;">
                <option value="">All Years</option>
                @foreach($availableYears as $yr)
                    <option value="{{ $yr }}">{{ $yr }}</option>
                @endforeach
            </select>
            <select wire:model.live="filter_month" class="form-control" style="width:auto;height:36px;font-size:0.82rem;padding:0 2rem 0 0.75rem;">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
                @endfor
            </select>
            @if($search || $filter_year || $filter_month)
                <button wire:click="clearFilters" type="button"
                        style="height:36px;padding:0 0.875rem;background:var(--bg-hover);border:1px solid var(--border-color);border-radius:var(--radius-sm);font-size:0.78rem;font-weight:600;color:var(--text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:0.35rem;white-space:nowrap;transition:all 0.15s;"
                        onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                        onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">
                    <i class="ph ph-x"></i> Clear Filters
                </button>
            @endif
        </div>

        {{-- Table --}}
        @if($reportsList->isEmpty())
            <div style="padding:4rem 2rem;text-align:center;color:var(--text-muted);">
                <div style="width:60px;height:60px;background:var(--bg-hover);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                    <i class="ph ph-folder-open" style="font-size:1.75rem;"></i>
                </div>
                @if($search || $filter_year)
                    <p style="font-size:0.95rem;font-weight:700;color:var(--text-secondary);margin-bottom:0.4rem;">No reports found for this filter</p>
                    <p style="font-size:0.82rem;margin-bottom:1.25rem;">Try adjusting your search or year filter.</p>
                    <button wire:click="clearFilters" type="button" class="btn btn-secondary" style="font-size:0.82rem;">
                        <i class="ph ph-funnel-x"></i> Clear Filters
                    </button>
                @else
                    <p style="font-size:0.95rem;font-weight:700;color:var(--text-secondary);margin-bottom:0.4rem;">No reports uploaded yet</p>
                    <p style="font-size:0.82rem;">Use the form above to upload your first report.</p>
                @endif
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:var(--bg-hover);border-bottom:2px solid var(--border-color);">
                            <th style="padding:0.875rem 1.5rem;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);white-space:nowrap;">Period</th>
                            <th style="padding:0.875rem 1.5rem;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);">Type / File</th>
                            <th style="padding:0.875rem 1.5rem;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);white-space:nowrap;">Uploaded By</th>
                            <th style="padding:0.875rem 1.5rem;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);">Status</th>
                            <th style="padding:0.875rem 1.5rem;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportsList as $report)
                            <tr style="border-bottom:1px solid var(--border-color);transition:background 0.12s;"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Period --}}
                                <td style="padding:1rem 1.5rem;white-space:nowrap;">
                                    <p style="font-size:0.875rem;font-weight:700;color:var(--text-primary);margin-bottom:0.15rem;">
                                        {{ \Carbon\Carbon::create()->month($report->month)->format('F') }} {{ $report->year }}
                                    </p>
                                    <p style="font-size:0.72rem;color:var(--text-muted);">
                                        {{ ($report->submitted_at ?? $report->created_at)->format('M d, Y · h:i A') }}
                                    </p>
                                </td>

                                {{-- Type / File --}}
                                <td style="padding:1rem 1.5rem;max-width:320px;">
                                    <p style="font-size:0.85rem;font-weight:600;color:var(--text-primary);margin-bottom:0.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $report->report_type }}
                                    </p>
                                    @if($report->file_path)
                                        <p style="font-size:0.72rem;color:var(--text-muted);display:flex;align-items:center;gap:0.3rem;margin-bottom:0.1rem;">
                                            <i class="ph ph-paperclip"></i>
                                            {{ $report->filename ?? basename($report->file_path) }}
                                        </p>
                                    @elseif($report->content)
                                        <p style="font-size:0.72rem;color:var(--warning);display:flex;align-items:center;gap:0.3rem;">
                                            <i class="ph ph-clock-counter-clockwise"></i> Legacy V1 Template
                                        </p>
                                    @endif
                                    @if($report->remarks)
                                        <p style="font-size:0.72rem;color:var(--text-muted);font-style:italic;">{{ $report->remarks }}</p>
                                    @endif
                                </td>

                                {{-- Uploaded By --}}
                                <td style="padding:1rem 1.5rem;white-space:nowrap;">
                                    <div style="display:flex;align-items:center;gap:0.6rem;">
                                        <div style="width:28px;height:28px;border-radius:50%;background:var(--accent-blue,#6366f1);color:white;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;flex-shrink:0;">
                                            {{ substr(optional($report->submitter)->name ?? '?', 0, 1) }}
                                        </div>
                                        <span style="font-size:0.82rem;color:var(--text-secondary);">{{ optional($report->submitter)->name ?? 'System' }}</span>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td style="padding:1rem 1.5rem;">
                                    <span style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.25rem 0.65rem;background:rgba(16,185,129,0.1);color:#059669;border-radius:999px;font-size:0.72rem;font-weight:700;">
                                        <i class="ph ph-check-circle"></i> Finalized
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td style="padding:1rem 1.5rem;">
                                    <div style="display:flex;align-items:center;gap:0.375rem;justify-content:flex-end;">
                                        @if($report->file_path)
                                            {{-- View --}}
                                            @if(str_ends_with(strtolower($report->file_path), '.pdf'))
                                                <a href="{{ route('pdf-viewer.report', $report->id) }}"
                                                   title="View PDF"
                                                   style="width:32px;height:32px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all 0.15s;"
                                                   onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                                                   onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-secondary)'">
                                                    <i class="ph ph-file-pdf" style="font-size:1rem;"></i>
                                                </a>
                                            @else
                                                <button type="button"
                                                        @click="openFileViaElectron('{{ route('reports.open', $report->id) }}')"
                                                        title="Open file"
                                                        style="width:32px;height:32px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;"
                                                        onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                                                        onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-secondary)'">
                                                    <i class="ph ph-arrow-square-out" style="font-size:1rem;"></i>
                                                </button>
                                            @endif

                                            {{-- Download --}}
                                            <button wire:click="download({{ $report->id }})"
                                                    title="Download"
                                                    style="width:32px;height:32px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;"
                                                    onmouseover="this.style.borderColor='var(--success)';this.style.color='var(--success)'"
                                                    onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-secondary)'">
                                                <i class="ph ph-download-simple" style="font-size:1rem;"></i>
                                            </button>

                                            {{-- Replace --}}
                                            <button wire:click="startReplace({{ $report->id }})"
                                                    title="Replace"
                                                    style="width:32px;height:32px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;"
                                                    onmouseover="this.style.borderColor='var(--accent-blue)';this.style.color='var(--accent-blue)'"
                                                    onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-secondary)'">
                                                <i class="ph ph-arrows-merge" style="font-size:1rem;"></i>
                                            </button>
                                        @endif

                                        {{-- Delete --}}
                                        <button type="button"
                                                @click="confirmDelete({{ $report->id }}, '{{ addslashes($report->report_type) }}')"
                                                title="Delete"
                                                style="width:32px;height:32px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-muted);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;"
                                                onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                                                onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-muted)'">
                                            <i class="ph ph-trash" style="font-size:1rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($reportsList->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-light);">
                    {{ $reportsList->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>

</div>
