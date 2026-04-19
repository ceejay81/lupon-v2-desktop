<div>
    <div class="landscape-form-container">
        <div class="case-header-pill">
            <div class="case-header-content">
                <div class="case-icon">✏️</div>
                <div class="case-info">
                    <h2 class="case-title">Edit Case Record</h2>
                    <p class="case-subtitle">Update the details of case {{ $case->case_number }}. Status changes are tracked automatically.</p>
                </div>
            </div>
        </div>

        <form wire:submit="submit" class="landscape-form">

            {{-- Parties Information Row --}}
            <div class="form-row">
                {{-- Complainant --}}
                <div class="form-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gray-100, #f3f4f6); margin-bottom: 20px; padding-bottom: 8px;">
                        <h3 class="section-title" style="border: none; margin: 0; padding: 0;">COMPLAINANT DETAILS</h3>
                        <button type="button" wire:click="addComplainant" class="btn" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; font-size: 0.75rem; padding: 6px 12px; border-radius: 99px; cursor: pointer; font-weight: 600;">+ Add Another</button>
                    </div>

                    @foreach($complainants as $index => $comp)
                    <div style="background: var(--gray-50, #f9fafb); padding: 16px; border-radius: 8px; margin-bottom: 16px; border: 1px solid var(--gray-200, #e5e7eb); position: relative;">
                        @if(count($complainants) > 1)
                            <button type="button" wire:click="removeComplainant({{ $index }})" style="position: absolute; right: -8px; top: -8px; background: #ef4444; color: white; border: none; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);"><i class="ph ph-x"></i></button>
                        @endif

                        <div class="form-group">
                            <label>Search Past Records</label>
                            @livewire('digital-blotter.party-search', ['partyType' => 'complainant', 'index' => $index], key('comp-edit-'.$index))
                            <small class="text-muted">Select to auto-fill, or type a new name below.</small>
                        </div>
                        <div class="form-columns">
                            <div class="form-group">
                                <label for="complainants.{{ $index }}.name">Full Name <span style="color: var(--danger, #ef4444);">*</span></label>
                                <input wire:model="complainants.{{ $index }}.name" type="text" id="complainants.{{ $index }}.name" placeholder="e.g. Juan Dela Cruz">
                                @error('complainants.'.$index.'.name') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label for="complainants.{{ $index }}.phone">Contact Number</label>
                                <input wire:model="complainants.{{ $index }}.phone" type="tel" id="complainants.{{ $index }}.phone" maxlength="11" placeholder="e.g. 09XX XXX XXXX">
                                @error('complainants.'.$index.'.phone') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label for="complainants.{{ $index }}.address">Address</label>
                            <input wire:model="complainants.{{ $index }}.address" type="text" id="complainants.{{ $index }}.address" placeholder="Purok / Street, Barangay Bula">
                            @error('complainants.'.$index.'.address') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Respondent --}}
                <div class="form-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gray-100, #f3f4f6); margin-bottom: 20px; padding-bottom: 8px;">
                        <h3 class="section-title" style="border: none; margin: 0; padding: 0;">RESPONDENT DETAILS</h3>
                        <button type="button" wire:click="addRespondent" class="btn" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; font-size: 0.75rem; padding: 6px 12px; border-radius: 99px; cursor: pointer; font-weight: 600;">+ Add Another</button>
                    </div>

                    @foreach($respondents as $index => $resp)
                    <div style="background: var(--gray-50, #f9fafb); padding: 16px; border-radius: 8px; margin-bottom: 16px; border: 1px solid var(--gray-200, #e5e7eb); position: relative;">
                        @if(count($respondents) > 1)
                            <button type="button" wire:click="removeRespondent({{ $index }})" style="position: absolute; right: -8px; top: -8px; background: #ef4444; color: white; border: none; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);"><i class="ph ph-x"></i></button>
                        @endif

                        <div class="form-group">
                            <label>Search Past Records</label>
                            @livewire('digital-blotter.party-search', ['partyType' => 'respondent', 'index' => $index], key('resp-edit-'.$index))
                            <small class="text-muted">Select to auto-fill, or type a new name below.</small>
                        </div>
                        <div class="form-columns">
                            <div class="form-group">
                                <label for="respondents.{{ $index }}.name">Full Name <span style="color: var(--danger, #ef4444);">*</span></label>
                                <input wire:model="respondents.{{ $index }}.name" type="text" id="respondents.{{ $index }}.name" placeholder="e.g. Maria Santos">
                                @error('respondents.'.$index.'.name') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label for="respondents.{{ $index }}.phone">Contact Number</label>
                                <input wire:model="respondents.{{ $index }}.phone" type="tel" id="respondents.{{ $index }}.phone" maxlength="11" placeholder="e.g. 09XX XXX XXXX">
                                @error('respondents.'.$index.'.phone') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label for="respondents.{{ $index }}.address">Address</label>
                            <input wire:model="respondents.{{ $index }}.address" type="text" id="respondents.{{ $index }}.address" placeholder="Purok / Street, Barangay Bula">
                            @error('respondents.'.$index.'.address') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Case Details Row --}}
            <div class="form-row">
                <div class="form-section full-width">
                    <h3 class="section-title">CASE DETAILS</h3>
                    <div class="form-columns-three">
                        <div class="form-group">
                            <label for="nature_of_case">Nature of Case <span style="color: var(--danger, #ef4444);">*</span></label>
                            <input wire:model="nature_of_case" type="text" id="nature_of_case" placeholder="e.g. Property Dispute, Collection of Debt, Boundary Issue">
                            <small class="text-muted">Enter the nature of the dispute (free text input)</small>
                            @error('nature_of_case') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label for="filed_date">Date Filed <span style="color: var(--danger, #ef4444);">*</span></label>
                            <input wire:model="filed_date" type="date" id="filed_date">
                            @error('filed_date') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description of Complaint</label>
                        <textarea wire:model="description" id="description" rows="4" placeholder="Describe the nature of the dispute..."></textarea>
                        @error('description') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('cases.show', $case) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <span wire:loading.remove wire:target="submit">
                        <i class="ph ph-floppy-disk"></i> Update Case Record
                    </span>
                    <span wire:loading wire:target="submit">
                        <i class="ph ph-spinner ph-spin"></i> Saving...
                    </span>
                </button>
            </div>

        </form>
    </div>

    <style>
    .landscape-form-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .case-header-pill {
        margin-bottom: 1.5rem;
    }

    .case-header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .case-header-content .case-icon {
        font-size: 2rem;
    }

    .case-header-content .case-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary, #1e293b);
        margin: 0;
    }

    .case-header-content .case-subtitle {
        font-size: 0.875rem;
        color: var(--text-muted, #94a3b8);
        margin: 0;
    }

    .landscape-form {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .form-row {
        display: flex;
        gap: 32px;
        align-items: flex-start;
    }

    .form-row .form-section {
        flex: 1;
        background: white;
        border-radius: var(--radius-lg, 12px);
        padding: 24px;
        border: 1px solid var(--gray-200, #e5e7eb);
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    .form-row .form-section.full-width {
        flex: none;
        width: 100%;
    }

    .form-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-columns-three {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-secondary, #64748b);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0 0 20px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--gray-100, #f3f4f6);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary, #1e293b);
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--gray-300, #d1d5db);
        border-radius: var(--radius-md, 8px);
        font-size: 0.875rem;
        color: var(--text-primary, #1e293b);
        background-color: var(--gray-50, #f9fafb);
        transition: all 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--accent-blue, #3b82f6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background-color: white;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-group .error {
        color: var(--danger, #ef4444);
        font-size: 0.8125rem;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .form-group small.text-muted {
        color: var(--text-muted, #94a3b8);
        font-size: 0.8125rem;
        margin-top: 4px;
        display: block;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 24px 0;
        border-top: 1px solid var(--gray-200, #e5e7eb);
        margin-top: 8px;
    }

    .btn-secondary,
    .btn-primary {
        padding: 12px 24px;
        border-radius: var(--radius-md, 8px);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-secondary {
        background-color: var(--gray-100, #f3f4f6);
        color: var(--text-secondary, #64748b);
        border: 1px solid var(--gray-300, #d1d5db);
    }

    .btn-secondary:hover {
        background-color: var(--gray-200, #e5e7eb);
        color: var(--text-primary, #1e293b);
    }

    .btn-primary {
        background-color: var(--accent-blue, #3b82f6);
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    @media (max-width: 1024px) {
        .form-row {
            flex-direction: column;
            gap: 24px;
        }
        .form-columns,
        .form-columns-three {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    @media (max-width: 768px) {
        .landscape-form-container {
            padding: 0 16px;
        }
        .form-row .form-section {
            padding: 20px;
        }
        .form-actions {
            flex-direction: column-reverse;
        }
        .btn-secondary,
        .btn-primary {
            width: 100%;
            justify-content: center;
        }
    }

    /* Dark Mode Overrides */
    [data-theme='dark'] .form-row .form-section {
        background: var(--bg-card, #1e293b);
        border-color: var(--border-medium, #334155);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    }

    [data-theme='dark'] .case-header-content .case-title,
    [data-theme='dark'] .form-group label {
        color: var(--text-primary, #f8fafc);
    }

    [data-theme='dark'] .section-title {
        color: var(--text-secondary, #94a3b8);
        border-bottom-color: var(--border-light, #334155);
    }

    [data-theme='dark'] .form-group input,
    [data-theme='dark'] .form-group select,
    [data-theme='dark'] .form-group textarea {
        background-color: #0f172a;
        border-color: #334155;
        color: #f8fafc;
    }

    [data-theme='dark'] .form-group input:focus,
    [data-theme='dark'] .form-group select:focus,
    [data-theme='dark'] .form-group textarea:focus {
        background-color: #1e293b;
        border-color: var(--accent-blue, #3b82f6);
    }

    [data-theme='dark'] .btn-secondary {
        background-color: #334155;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-theme='dark'] .btn-secondary:hover {
        background-color: #475569;
    }

    [data-theme='dark'] .form-actions {
        border-top-color: var(--border-light, #334155);
    }

    [data-theme='dark'] .text-muted {
        color: #94a3b8 !important;
    }
    </style>
</div>
