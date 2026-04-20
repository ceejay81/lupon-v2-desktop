{{-- System Maintenance Tab --}}
<div class="settings-tab-panel" id="tab-maintenance" style="display:none;">
    {{-- Backup Hero Card --}}
    <div class="settings-backup-hero" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; position: relative; overflow: hidden; border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-lg);">
        <i class="ph ph-shield-check" style="position: absolute; right: -30px; top: -30px; font-size: 15rem; color: rgba(59, 130, 246, 0.05); transform: rotate(-10deg);"></i>
        
        <div style="position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 24px;">
            <div>
                <h3 style="font-size: 0.75rem; font-weight: 800; color: var(--accent-blue); text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 20px; height: 1px; background: var(--accent-blue);"></span> Recovery Center
                </h3>
                <div style="font-size: 2.25rem; font-weight: 800; color: white; margin: 0 0 12px 0; letter-spacing: -0.02em; line-height: 1.1;">System Snapshots</div>
                <p style="font-size: 0.9375rem; color: #94a3b8; max-width: 480px; line-height: 1.6; margin-bottom: 24px;">
                    Capture every record, document, and custom setting in a single encrypted bundle. Perfect for external storage or disaster recovery.
                </p>
                <div style="display: flex; gap: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 0.8125rem; font-weight: 600;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #34d399;"></span> USB Safe
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 0.8125rem; font-weight: 600;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #60a5fa;"></span> Multi-Part Logic
                    </div>
                </div>
            </div>
            
            <form action="{{ route('settings.maintenance') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="backup">
                <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 16px 32px; height: auto; font-size: 1rem; border: none; box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);">
                    <i class="ph ph-cloud-arrow-down" style="font-size: 1.5rem;"></i> Generate Backup
                </button>
            </form>
        </div>
    </div>

    {{-- Restore Card --}}
    <div class="card" style="margin-top: 2rem; border: 1px solid var(--gray-200); position: relative;" x-data="{ confirmRestore: false, isRestoring: false, hasFile: false, fileName: '' }">
        <div class="card-header" style="background: var(--bg-card); border-bottom: 1px solid var(--gray-100); padding: 24px 32px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.1); color: var(--warning); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="ph ph-upload-simple" style="font-size: 1.25rem;"></i>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0;">Restore from Backup</h2>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0;">Upload a system snapshot to roll back changes</p>
                </div>
            </div>
        </div>
        <div class="card-body" style="padding: 32px;">
            <div style="display: flex; gap: 32px; align-items: flex-start; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <form id="restore-form" action="{{ route('settings.maintenance.restore') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="action" value="restore">
                        
                        <div style="position: relative; margin-bottom: 24px;">
                            <label for="restore-file-input" style="display: block; cursor: pointer;">
                                <div :class="hasFile ? 'border-success' : 'border-dashed'" 
                                     style="border: 2px dashed var(--gray-200); border-radius: 12px; padding: 40px 24px; text-align: center; transition: all 0.2s; background: var(--gray-50);">
                                    <div x-show="!hasFile">
                                        <i class="ph ph-file-zip" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px;"></i>
                                        <p style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin: 0;">Click to select backup file</p>
                                        <p style="font-size: 0.75rem; color: var(--text-muted); margin: 4px 0 0 0;">Only .zip files are accepted</p>
                                    </div>
                                    <div x-show="hasFile" style="animation: fadeIn 0.3s ease-out;">
                                        <i class="ph ph-check-circle" style="font-size: 2.5rem; color: var(--success); margin-bottom: 12px;"></i>
                                        <p style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin: 0;" x-text="fileName"></p>
                                        <p style="font-size: 0.75rem; color: var(--accent-blue); margin: 4px 0 0 0; font-weight: 600;" @click.prevent="hasFile = false; $refs.fileInput.value = ''">Change file</p>
                                    </div>
                                </div>
                                <input type="file" name="backup_file" accept=".zip"
                                   required x-ref="fileInput" id="restore-file-input" style="display: none;"
                                   @change="if($refs.fileInput.files.length) { hasFile = true; fileName = $refs.fileInput.files[0].name; }">
                            </label>
                        </div>

                        <button type="button" class="btn-primary"
                                style="width: 100%; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; font-size: 0.9375rem; font-weight: 700; box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.3);"
                                @click="if ($refs.fileInput.files.length) { confirmRestore = true; } else { $refs.fileInput.click(); }">
                            <i class="ph ph-arrow-counter-clockwise"></i> Start System Restore
                        </button>
                        
                        @error('backup_file') <p class="form-error" style="margin-top: 1rem;"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </form>
                </div>

                <div style="width: 280px; background: var(--gray-50); border-radius: 12px; padding: 24px;">
                    <h4 style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class="ph ph-info-circle"></i> Vital Info
                    </h4>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
                        <li style="font-size: 0.8125rem; color: var(--text-secondary); line-height: 1.4; display: flex; gap: 10px;">
                            <i class="ph ph-shield-check" style="color: var(--success); font-size: 1.125rem; flex-shrink: 0;"></i>
                            An emergency rollback image is created automatically before restore begins.
                        </li>
                        <li style="font-size: 0.8125rem; color: var(--text-secondary); line-height: 1.4; display: flex; gap: 10px;">
                            <i class="ph ph-clock-counter-clockwise" style="color: var(--accent-blue); font-size: 1.125rem; flex-shrink: 0;"></i>
                            Typical restore time is 30-60 seconds depending on record volume.
                        </li>
                    </ul>
                </div>
            </div>

            @if(session('restore_debug'))
                <div style="margin-top: 1rem; padding: 1rem; background: #f1f5f9; border-radius: 0.5rem; font-size: 0.75rem; font-family: monospace;">
                    <strong>Debug Info:</strong><br>
                    {{ session('restore_debug') }}
                </div>
            @endif
        </div>

        {{-- Confirm Modal --}}
        <template x-if="confirmRestore">
            <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
                <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 420px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                    <div style="padding: 1.75rem 2rem;">
                        <div style="width: 48px; height: 48px; background: var(--warning-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                            <i class="ph ph-warning" style="font-size: 1.5rem; color: var(--warning);"></i>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Perform Full System Restore?</h3>
                        <p style="font-size: 0.875rem; color: var(--text-secondary);">This will replace the current database and all media files with the content of the backup. An **Emergency Rollback** will be saved automatically.</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.75rem;">
                            <i class="ph ph-info"></i> Use the <strong>lupon_backup_...</strong> file (not EMERGENCY_ROLLBACK)
                        </p>
                    </div>
                    <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                        <button @click="confirmRestore = false" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                            Cancel
                        </button>
                        <button @click="
                            isRestoring = true;
                            confirmRestore = false;
                            const form = document.getElementById('restore-form');
                            if (form) {
                                setTimeout(() => form.submit(), 50);
                            } else {
                                isRestoring = false;
                                alert('Error: Restore form not found. Please refresh the page.');
                            }
                        " type="button" :disabled="isRestoring"
                            style="height: 40px; padding: 0 1.25rem; background: var(--warning); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph ph-arrow-counter-clockwise"></i>
                            <span x-text="isRestoring ? 'Restoring...' : 'Yes, Restore'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- Loading Overlay --}}
        <template x-teleport="body">
            <div x-show="isRestoring" x-cloak class="full-screen-loading">
                <div class="loading-spinner"></div>
                <div class="loading-text-container">
                    <h2>Restoring Database...</h2>
                    <p>The system is currently restoring your snapshot. This typically takes 30-60 seconds. <br><strong>Please do not close this window.</strong></p>
                </div>
            </div>
        </template>
    </div>

    {{-- Backup History Component --}}
    @include('settings.partials.backup-history')
</div>
