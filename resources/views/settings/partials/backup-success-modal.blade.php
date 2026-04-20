{{-- Post-Backup Success Modal --}}
@if(session('backup_success'))
@push('modals')
<div x-data="{ show: true }" x-show="show">
    <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.8); backdrop-filter: blur(8px); z-index: 999998;"></div>
    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 999999; width: 100%; max-width: 440px; padding: 1rem;">
        <div class="card" style="width: 100%; text-align: center; padding: 2.5rem 2rem; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
            <div style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="ph ph-check-circle" style="font-size: 2.5rem;"></i>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">System Snapshot Complete</h2>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 2rem;">A secure record of your data has been saved to the server.</p>
            
            <div style="background: var(--gray-50); border-radius: 1rem; padding: 1.25rem; margin-bottom: 2rem; border: 1px dashed var(--gray-200);">
                <h3 style="font-size: 0.75rem; font-weight: 800; color: var(--warning); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="ph ph-warning-circle"></i> Vital Safety Step
                </h3>
                <p style="font-size: 0.8125rem; color: var(--text-primary); line-height: 1.5; font-weight: 600;">
                    Download this file now and save it to a <span style="color: var(--accent-blue);">USB Drive</span> or <span style="color: var(--success);">Google Drive</span>.
                </p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('settings.maintenance.download', ['action' => 'download', 'filename' => session('latest_backup')]) }}" 
                   class="btn-primary" 
                   style="height: 48px; display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--accent-blue); font-size: 0.875rem;">
                    <i class="ph ph-download-simple" style="font-size: 1.25rem;"></i> Download to This Computer
                </a>
                <button @click="show = false" 
                        class="btn-secondary" 
                        style="height: 44px; border: none; font-size: 0.8125rem;">
                    I'll do it later
                </button>
            </div>
        </div>
    </div>
</div>
@endpush
@endif
