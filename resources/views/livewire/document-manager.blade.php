<div x-data="{
    confirmDocId: null,
    isDragging: false,
    openFileViaElectron(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.path && window.electron && window.electron.openFile) {
                    window.electron.openFile(data.path);
                } else if (data.path) {
                    // Fallback: trigger download if not in Electron
                    window.location.href = url.replace('/open', '/download');
                } else {
                    alert('Could not resolve file path: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(() => {
                window.location.href = url.replace('/open', '/download');
            });
    }
}">

    {{-- ─── Delete Confirmation Modal ─── --}}
    <template x-if="confirmDocId !== null">
        <div style="position:fixed;inset:0;background:rgba(15,23,42,0.65);backdrop-filter:blur(8px);z-index:9000;display:flex;align-items:center;justify-content:center;padding:1rem;" @keydown.escape.window="confirmDocId=null">
            <div style="background:var(--bg-card);border-radius:1.25rem;box-shadow:0 32px 64px -12px rgba(0,0,0,0.4);width:100%;max-width:420px;overflow:hidden;animation:modal-slide-up 0.2s ease-out;">
                <div style="padding:2rem;">
                    <div style="width:52px;height:52px;background:#fef2f2;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                        <i class="ph ph-trash" style="font-size:1.6rem;color:#ef4444;"></i>
                    </div>
                    <h3 style="font-size:1.05rem;font-weight:800;color:var(--text-primary);margin-bottom:0.5rem;">Delete Document?</h3>
                    <p style="font-size:0.875rem;color:var(--text-secondary);line-height:1.5;">This will permanently remove the file from the server and cannot be undone.</p>
                </div>
                <div style="padding:0 2rem 1.75rem;display:flex;gap:0.75rem;justify-content:flex-end;">
                    <button @click="confirmDocId=null" type="button" class="btn-sm" style="height:40px;padding:0 1.25rem;">
                        Cancel
                    </button>
                    <button @click="$wire.deleteDocument(confirmDocId); confirmDocId=null" type="button"
                        style="height:40px;padding:0 1.25rem;background:#ef4444;color:white;border:none;border-radius:var(--radius-sm);font-size:0.8rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;transition:background 0.2s;">
                        <i class="ph ph-trash"></i> Delete
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
                        A file named <strong>{{ $duplicateInfo['filename'] }}</strong> already exists in this case. What would you like to do?
                    </p>
                </div>

                {{-- Comparison Cards --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;padding:0 2rem 1.5rem;">
                    {{-- Existing --}}
                    <div style="padding:0.875rem;background:var(--bg-hover);border:1px solid var(--border-color);border-radius:var(--radius-md);">
                        <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);margin-bottom:0.5rem;">Existing File</p>
                        <p style="font-size:0.78rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;word-break:break-all;">{{ $duplicateInfo['filename'] }}</p>
                        <p style="font-size:0.72rem;color:var(--text-muted);">{{ $duplicateInfo['size'] }}</p>
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
                    <button wire:click="saveAsNewDocument" type="button"
                        style="height:38px;padding:0 1.1rem;background:var(--bg-hover);border:1px solid var(--border-color);border-radius:var(--radius-sm);font-size:0.8rem;font-weight:700;color:var(--text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:0.4rem;transition:all 0.15s;"
                        onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                        onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">
                        <i class="ph ph-copy"></i> Save as New
                    </button>
                    <button wire:click="replaceDocument" type="button"
                        style="height:38px;padding:0 1.1rem;background:#ef4444;color:white;border:none;border-radius:var(--radius-sm);font-size:0.8rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.4rem;">
                        <i class="ph ph-arrows-merge"></i> Replace
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Upload Button & Form Toggle ─── --}}
    <div style="margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;">
        <p style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);">
            {{ $this->selectedCase->documents->count() }} {{ Str::plural('file', $this->selectedCase->documents->count()) }} attached
        </p>
        <button wire:click="toggleUploadForm" type="button"
            style="display:inline-flex;align-items:center;gap:0.45rem;padding:0.5rem 1.1rem;background:{{ $showUploadForm ? 'var(--bg-hover)' : 'var(--accent-blue, #3b82f6)' }};color:{{ $showUploadForm ? 'var(--text-secondary)' : 'white' }};border:1px solid {{ $showUploadForm ? 'var(--border-color, #e2e8f0)' : 'transparent' }};border-radius:var(--radius-md);font-size:0.8rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
            <i class="ph {{ $showUploadForm ? 'ph-x' : 'ph-upload-simple' }}"></i>
            {{ $showUploadForm ? 'Cancel' : 'Upload Document' }}
        </button>
    </div>

    {{-- ─── Upload Form ─── --}}
    @if($showUploadForm)
        <form wire:key="document-upload-form" wire:submit.prevent="uploadDocument"
            x-on:dragover.prevent="isDragging=true"
            x-on:dragleave.prevent="isDragging=false"
            x-on:drop.prevent="isDragging=false"
            :style="isDragging ? 'border-color:var(--primary);background:var(--bg-hover);' : ''"
            style="margin-bottom:1.5rem;padding:1.5rem;background:var(--bg-hover);border:2px dashed var(--border-color);border-radius:var(--radius-lg);transition:all 0.2s;">

            <p style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);margin-bottom:1.25rem;">
                <i class="ph ph-upload-simple"></i> New Upload
            </p>

            <div style="display:grid;grid-template-columns:1fr;gap:0.875rem;margin-bottom:0.875rem;">
                {{-- Document Type (Typable) --}}
                <div>
                    <label class="form-label">Classification / Type <span style="color:var(--danger);">*</span></label>
                    <input wire:key="input-document-type"
                           wire:model="document_type"
                           list="doc-type-list"
                           type="text"
                           class="form-control"
                           placeholder="Type or select... (e.g. Summons, Minutes)"
                           autocomplete="off">
                    <datalist id="doc-type-list">
                        @foreach($typeSuggestions as $suggestion)
                            <option value="{{ $suggestion }}">
                        @endforeach
                    </datalist>
                    @error('document_type') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr;margin-bottom:0.875rem;">
                {{-- File Picker --}}
                <div>
                    <label class="form-label">
                        Source File <span style="color:var(--text-muted);font-weight:400;">(PDF, DOCX, Images · max 20 MB)</span>
                    </label>
                    <div style="padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">
                            @if($file)
                                <i class="ph ph-file-check" style="color:var(--primary);"></i> {{ $file->getClientOriginalName() }}
                            @else
                                <i class="ph ph-file-plus"></i> Select or drop file
                            @endif
                        </span>
                        <label for="docFile" class="btn-sm" style="cursor: pointer; background: var(--bg-hover);">
                            Browse
                        </label>
                    </div>
                    <input wire:model="file" type="file" id="docFile" style="display:none;"
                           accept=".pdf,.doc,.docx,.odt,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp">
                    @error('file') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Remarks --}}
            <div style="margin-bottom:1rem;">
                <label class="form-label">Remarks <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
                <input wire:key="input-remarks" wire:model="remarks" type="text" class="form-control" placeholder="Brief note about this document...">
                @error('remarks') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div style="display:flex;justify-content:flex-end;gap:0.625rem;">
                <button type="button" wire:click="toggleUploadForm" class="btn-sm" style="height:40px;padding:0 1.25rem;">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" style="height:40px;padding:0 1.5rem;font-size:0.82rem;">
                    <span wire:loading.remove wire:target="uploadDocument">
                        <i class="ph ph-upload-simple"></i> Upload
                    </span>
                    <span wire:loading wire:target="uploadDocument">
                        <i class="ph ph-circle-notch ph-spin"></i> Uploading...
                    </span>
                </button>
            </div>
        </form>
    @endif

    {{-- ─── Document List ─── --}}
    @if($this->selectedCase && $this->selectedCase->documents->count())
        <div style="display:flex;flex-direction:column;gap:0.5rem;">
            @foreach($this->selectedCase->documents->sortByDesc('created_at') as $doc)
                <div style="display:flex;align-items:center;gap:0.875rem;padding:0.875rem 1.1rem;border:1px solid var(--border-color);border-radius:var(--radius-md);background:var(--bg-card);transition:border-color 0.15s,box-shadow 0.15s;"
                     onmouseover="this.style.borderColor='var(--border-accent)';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'"
                     onmouseout="this.style.borderColor='var(--border-color)';this.style.boxShadow='none'">

                    {{-- File Type Icon --}}
                    <div style="width:40px;height:40px;border-radius:10px;background:{{ $doc->file_icon_color }}18;border:1px solid {{ $doc->file_icon_color }}30;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ph {{ $doc->file_icon }}" style="font-size:1.3rem;color:{{ $doc->file_icon_color }};"></i>
                    </div>

                    {{-- File Info --}}
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:0.85rem;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:0.15rem;">
                            {{ $doc->filename }}
                        </p>
                        <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                            @if($doc->document_type)
                                <span style="font-size:0.7rem;font-weight:600;padding:0.15rem 0.5rem;background:var(--bg-hover);border:1px solid var(--border-light);border-radius:4px;color:var(--text-secondary);">
                                    {{ $doc->document_type }}
                                </span>
                            @endif
                            <span style="font-size:0.7rem;color:var(--text-muted);">{{ $doc->file_size_formatted }}</span>
                            <span style="font-size:0.7rem;color:var(--text-muted);">•</span>
                            <span style="font-size:0.7rem;color:var(--text-muted);">{{ $doc->created_at->format('M d, Y') }}</span>
                            @if($doc->uploader)
                                <span style="font-size:0.7rem;color:var(--text-muted);">• {{ $doc->uploader->name }}</span>
                            @endif
                        </div>
                        @if($doc->remarks)
                            <p style="font-size:0.72rem;color:var(--text-muted);margin-top:0.2rem;font-style:italic;">{{ $doc->remarks }}</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;align-items:center;gap:0.375rem;flex-shrink:0;">
                        {{-- Open in Internal PDF Viewer or External App --}}
                        @if(strtolower($doc->mime_type) === 'application/pdf' || str_ends_with(strtolower($doc->filename), '.pdf'))
                            <a href="{{ route('pdf-viewer.document', $doc->id) }}"
                               title="View PDF Document"
                               style="width:34px;height:34px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all 0.15s;"
                               onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                               onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-secondary)'">
                                <i class="ph ph-file-pdf" style="font-size:1.1rem;"></i>
                            </a>
                        @else
                            <button type="button"
                                    @click="openFileViaElectron('{{ $doc->open_url }}')"
                                    title="Open in external app"
                                    style="width:34px;height:34px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;"
                                    onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                                    onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-secondary)'">
                                <i class="ph ph-arrow-square-out" style="font-size:1rem;"></i>
                            </button>
                        @endif

                        {{-- Download --}}
                        <a href="{{ $doc->download_url }}"
                           download
                           title="Download"
                           style="width:34px;height:34px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all 0.15s;"
                           onmouseover="this.style.borderColor='var(--success)';this.style.color='var(--success)'"
                           onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-secondary)'">
                            <i class="ph ph-download-simple" style="font-size:1rem;"></i>
                        </a>

                        {{-- Delete --}}
                        <button type="button"
                                @click="confirmDocId = {{ $doc->id }}"
                                title="Delete"
                                style="width:34px;height:34px;border:1px solid var(--border-light);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-muted);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;"
                                onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                                onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-muted)'">
                            <i class="ph ph-trash" style="font-size:1rem;"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div style="text-align:center;padding:2.5rem 1rem;border:2px dashed var(--border-color);border-radius:var(--radius-lg);color:var(--text-muted);">
            <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="ph ph-folder-open" style="font-size:1.75rem;color:var(--text-muted);"></i>
            </div>
            <p style="font-size:0.9rem;font-weight:600;color:var(--text-secondary);margin-bottom:0.35rem;">No documents yet</p>
            <p style="font-size:0.8rem;">Upload case files — DOCX, PDF, ODT, images and more.</p>
        </div>
    @endif

</div>
