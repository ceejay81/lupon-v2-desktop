{{-- Premium Branding Tab --}}
@if(\App\Models\Setting::get('is_branding_enabled'))
<div class="settings-tab-panel" id="tab-branding" style="display:none;">
    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 24px; border: none;">
            <h2 class="card-title" style="color: white;"><i class="ph-duotone ph-paint-brush"></i> Barangay Logo Customization</h2>
            <p style="color: rgba(255,255,255,0.8); font-size: 0.8125rem; margin-top: 4px;">Customize the official branding of the Lupon Management System.</p>
        </div>
        <div class="card-body" style="padding: 32px;">
            <div style="display: grid; grid-template-columns: 240px 1fr; gap: 3rem; align-items: center;">
                <div style="text-align: center;">
                    <p style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Current Visual Identity</p>
                    <div style="width: 180px; height: 180px; background: var(--gray-50); border-radius: 24px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px dashed var(--gray-200); margin: 0 auto; padding: 20px;">
                        @php $logoPath = $settings['barangay_logo_path'] ?? null; @endphp
                        @if($logoPath)
                            <img src="{{ asset($logoPath) }}" alt="Current Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        @else
                            <div style="text-align: center;">
                                <i class="ph ph-image-square" style="font-size: 3.5rem; color: var(--gray-200);"></i>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">No logo uploaded</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <form action="{{ route('settings.update-branding') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="settings-field" style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 12px; text-transform: uppercase;">Official Logo File</label>
                            <div style="position: relative;">
                                <input type="file" name="logo" id="logo-input" accept="image/*" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2;" required>
                                <div style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 12px; transition: all 0.2s;">
                                    <div style="width: 40px; height: 40px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent-blue); border: 1px solid var(--gray-200);">
                                        <i class="ph ph-upload-simple"></i>
                                    </div>
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary);">Click to choose a file...</span>
                                </div>
                            </div>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 12px; line-height: 1.5;">
                                <i class="ph ph-info" style="color: var(--accent-blue);"></i> Recommended: High-resolution PNG or SVG with a transparent background. Max file size: 2MB.
                            </p>
                        </div>
                        <div style="padding-top: 8px; border-top: 1px solid var(--gray-100);">
                            <button type="submit" class="btn-primary" style="background: var(--accent-blue); border-radius: 12px; padding: 12px 24px;">
                                <i class="ph ph-check-circle"></i> Save Visual Identity
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
