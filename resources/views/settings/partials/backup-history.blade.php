{{-- Backup History --}}
<div class="card" style="margin-top: 1.5rem;" 
     x-data="{ 
        confirmDelete: false, 
        deleteFile: '', 
        selectedFiles: [], 
        confirmBatchDelete: false,
        allFiles: @js(array_column($backups, 'name')),
        toggleAll() {
            if (this.selectedFiles.length === this.allFiles.length) {
                this.selectedFiles = [];
            } else {
                this.selectedFiles = [...this.allFiles];
            }
        }
     }">
    <div class="card-header" style="background: var(--bg-card); border-bottom: 1px solid var(--gray-100); padding: 24px 32px; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.1); color: var(--accent-blue); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="ph ph-database" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <h2 class="card-title" style="margin: 0;">Backup History</h2>
                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ count($backups) }} snapshots available</span>
            </div>
        </div>
        
        <div x-show="selectedFiles.length > 0" style="display: flex; align-items: center; gap: 16px; animation: modal-slide-up 0.2s ease-out; background: var(--danger-light); padding: 8px 16px; border-radius: var(--radius-md); border: 1px solid rgba(239, 68, 68, 0.2);">
            <span style="font-size: 0.8125rem; font-weight: 700; color: var(--danger);">
                <i class="ph ph-check-square"></i> <span x-text="selectedFiles.length"></span> selected
            </span>
            <button type="button" class="btn-sm" style="background: var(--danger); color: white; border: none; padding: 6px 12px; font-weight: 700; border-radius: 6px;" @click="confirmBatchDelete = true">
                <i class="ph ph-trash"></i> Delete
            </button>
        </div>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table class="settings-table">
            <thead>
                <tr>
                    <th style="width: 48px; text-align: center;">
                        <input type="checkbox" 
                               :checked="selectedFiles.length === allFiles.length && allFiles.length > 0"
                               @click="toggleAll()"
                               style="width: 17px; height: 17px; cursor: pointer; accent-color: var(--accent-blue);">
                    </th>
                    <th>Backup File Name</th>
                    <th>Size</th>
                    <th>Date Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                <tr :style="selectedFiles.includes('{{ $backup['name'] }}') ? 'background: rgba(239, 68, 68, 0.04)' : ''">
                    <td style="text-align: center;">
                        <input type="checkbox" value="{{ $backup['name'] }}" x-model="selectedFiles" 
                               style="width: 17px; height: 17px; cursor: pointer; accent-color: var(--accent-blue);">
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 36px; height: 36px; background: var(--gray-100); color: var(--accent-blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;">
                                <i class="ph ph-file-zip" style="font-size: 1.25rem;"></i>
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 700; color: var(--text-primary); font-size: 0.875rem;">{{ $backup['name'] }}</span>
                                @if(str_starts_with($backup['name'], 'EMERGENCY_ROLLBACK'))
                                    <span style="font-size: 0.65rem; font-weight: 800; color: var(--danger); text-transform: uppercase; letter-spacing: 0.05em;">Auto-Recovery Image</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size: 0.8125rem; font-weight: 600; color: var(--text-secondary);">{{ $backup['size'] }}</td>
                    <td style="font-size: 0.8125rem; color: var(--text-muted);">{{ $backup['date'] }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            @if(!str_starts_with($backup['name'], 'EMERGENCY_ROLLBACK'))
                            <button type="button" class="btn-sm" style="color: var(--warning);" title="Restore this backup"
                                    @click="
                                        const input = document.getElementById('restore-file-input');
                                        alert('Please use the file picker above and select: {{ $backup['name'] }}');
                                        input.focus();
                                        input.click();
                                    ">
                                <i class="ph ph-arrow-counter-clockwise"></i>
                            </button>
                            @endif
                            <a href="{{ route('settings.maintenance.download', ['action' => 'download', 'filename' => $backup['name']]) }}"
                               class="btn-sm" title="Download">
                                <i class="ph ph-download-simple"></i>
                            </a>
                            
                            <button type="button" class="btn-sm" style="color: var(--danger);" title="Delete"
                                    @click="confirmDelete = true; deleteFile = '{{ $backup['name'] }}'">
                                <i class="ph ph-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 48px; color: var(--text-muted);">
                        <i class="ph ph-database" style="font-size: 2.5rem; opacity: 0.3; display: block; margin-bottom: 8px;"></i>
                        No backups yet. Click "Create Backup Now" to get started.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Delete Confirm Modal --}}
    <template x-if="confirmDelete">
        <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 420px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                <div style="padding: 1.75rem 2rem;">
                    <div style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                        <i class="ph ph-trash" style="font-size: 1.5rem; color: var(--danger);"></i>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Delete Backup?</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">Are you sure you want to delete <strong x-text="deleteFile"></strong>? This action cannot be undone.</p>
                </div>
                <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button @click="confirmDelete = false" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                        Cancel
                    </button>
                    <form action="{{ route('settings.maintenance') }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="filename" :value="deleteFile">
                        <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--danger); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph ph-trash"></i> Yes, Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- Batch Delete Confirm Modal --}}
    <template x-if="confirmBatchDelete">
        <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 420px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                <div style="padding: 1.75rem 2rem;">
                    <div style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                        <i class="ph ph-trash" style="font-size: 1.5rem; color: var(--danger);"></i>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Delete Multiple Backups?</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">You are about to delete <strong x-text="selectedFiles.length"></strong> files permanently. This action cannot be undone.</p>
                </div>
                <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button @click="confirmBatchDelete = false" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                        Cancel
                    </button>
                    <form action="{{ route('settings.maintenance') }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="batch_delete">
                        <template x-for="file in selectedFiles" :key="file">
                            <input type="hidden" name="filenames[]" :value="file">
                        </template>
                        <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--danger); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph ph-trash"></i> Yes, Delete All Selected
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
