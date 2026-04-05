<div x-data="{ confirmDocId: null }">

    {{-- Custom Delete Confirm Modal --}}
    <template x-if="confirmDocId !== null">
        <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 400px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                <div style="padding: 1.75rem 2rem;">
                    <div style="width: 48px; height: 48px; background: var(--danger-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                        <i class="ph ph-file-x" style="font-size: 1.5rem; color: var(--danger);"></i>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Delete Document?</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">This will permanently remove the document and its file. This cannot be undone.</p>
                </div>
                <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button @click="confirmDocId = null" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                        Cancel
                    </button>
                    <button @click="$wire.deleteDocument(confirmDocId); confirmDocId = null" type="button"
                        style="height: 40px; padding: 0 1.25rem; background: var(--danger); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ph ph-trash"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Completeness bar --}}
    @if($this->selectedCase)
        @php $pct = $this->completeness; @endphp
        <div style="margin-bottom: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">MOV 2 Completeness</span>
                <span style="font-size: 0.75rem; font-weight: 700; color: {{ $pct === 100 ? 'var(--success)' : 'var(--warning)' }};">{{ $pct }}%</span>
            </div>
            <div class="progress-container">
                <div class="progress-bar {{ $pct === 100 ? 'progress-success' : 'progress-warning' }}" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    @endif

    {{-- Upload form --}}
    <form wire:submit="uploadDocument" style="margin-bottom: 1.25rem; padding: 1rem; background: var(--bg-hover); border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
        <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.75rem;">
            <i class="ph ph-upload-simple"></i> Upload Document
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
            <div>
                <label class="form-label">Document Type</label>
                <select wire:model="document_type" class="form-control">
                    <option value="">— Select type —</option>
                    @foreach($available_docs as $doc)
                        <option value="{{ $doc }}">{{ $doc }}</option>
                    @endforeach
                </select>
                @error('document_type') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">File <span style="color:var(--text-muted); font-weight:400;">(max 10MB)</span></label>
                <input wire:model="file" type="file" class="form-control" style="padding: 0.4rem 0.875rem;">
                @error('file') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.5rem 1rem;">
                <span wire:loading.remove wire:target="uploadDocument"><i class="ph ph-upload-simple"></i> Upload</span>
                <span wire:loading wire:target="uploadDocument"><i class="ph ph-spinner ph-spin"></i> Uploading...</span>
            </button>
        </div>
    </form>

    {{-- Document list --}}
    @if($this->selectedCase && $this->selectedCase->documents->count())
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @foreach($this->selectedCase->documents as $doc)
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-card);">
                    {{-- Preview icon or thumbnail --}}
                    @php 
                        $isDigital = $doc->file_path === 'digital_record';
                        $isImage = !$isDigital && in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                    @endphp

                    @if($isDigital)
                        <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: #f0fdf4; color: #15803d; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #bbf7d0;">
                            <i class="ph ph-article" style="font-size: 1.1rem;"></i>
                        </div>
                    @elseif($isImage)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($doc->file_path) }}" style="width: 32px; height: 32px; border-radius: var(--radius-sm); object-fit: cover; flex-shrink: 0; border: 1px solid var(--border-light);">
                    @else
                        <i class="ph ph-file-pdf" style="font-size: 1.25rem; color: var(--danger); flex-shrink: 0;"></i>
                    @endif
                    
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $doc->filename }}</p>
                        <p style="font-size: 0.72rem; color: var(--text-muted);">{{ $doc->document_type }}</p>
                    </div>
                    <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                        <a href="{{ $doc->viewer_url }}"
                           style="padding: 4px 8px; border: 1px solid var(--border-light); border-radius: var(--radius-sm); font-size: 0.72rem; color: var(--text-secondary); text-decoration: none; background: var(--bg-card);">
                            <i class="ph ph-eye"></i>
                        </a>
                        <button @click="confirmDocId = {{ $doc->id }}" type="button"
                                style="padding: 4px 8px; border: 1px solid var(--border-light); border-radius: var(--radius-sm); font-size: 0.72rem; color: var(--danger); background: var(--bg-card); cursor: pointer;">
                            <i class="ph ph-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 1.5rem 0; color: var(--text-muted);">
            <i class="ph ph-files" style="font-size: 1.75rem; display: block; margin-bottom: 0.5rem;"></i>
            <p style="font-size: 0.8rem;">No documents uploaded yet.</p>
        </div>
    @endif
</div>

