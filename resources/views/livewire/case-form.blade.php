<div>
    <div class="landscape-form-container">
        <div class="case-header-pill">
            <div class="case-header-content">
                <div class="case-icon">📝</div>
                <div class="case-info">
                    <h2 class="case-title">Case Filing Form</h2>
                    <p class="case-subtitle">Register a new dispute. Blotter ID is auto-suggested but editable.</p>
                </div>
            </div>
        </div>

        <form wire:submit="submit" class="landscape-form">

            {{-- Parties Information Row --}}
            <div class="form-row">
                {{-- Complainant --}}
                <div class="form-section">
                    <h3 class="section-title">COMPLAINANT DETAILS</h3>
                    <div class="form-group">
                        <label>Search Past Records</label>
                        @livewire('digital-blotter.party-search', ['partyType' => 'complainant'], 'complainant-search')
                        <small class="text-muted">Select to auto-fill, or type a new name below.</small>
                    </div>
                    <div class="form-columns">
                        <div class="form-group">
                            <label for="complainant">Full Name <span style="color: var(--danger, #ef4444);">*</span></label>
                            <input wire:model="complainant" type="text" id="complainant" placeholder="e.g. Juan Dela Cruz">
                            @error('complainant') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label for="complainant_phone">Contact Number</label>
                            <input wire:model="complainant_phone" type="tel" id="complainant_phone" maxlength="11" placeholder="e.g. 09XX XXX XXXX">
                            @error('complainant_phone') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="complainant_address">Address</label>
                        <input wire:model="complainant_address" type="text" id="complainant_address" placeholder="Purok / Street, Barangay Bula">
                        @error('complainant_address') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Respondent --}}
                <div class="form-section">
                    <h3 class="section-title">RESPONDENT DETAILS</h3>
                    <div class="form-group">
                        <label>Search Past Records</label>
                        @livewire('digital-blotter.party-search', ['partyType' => 'respondent'], 'respondent-search')
                        <small class="text-muted">Select to auto-fill, or type a new name below.</small>
                    </div>
                    <div class="form-columns">
                        <div class="form-group">
                            <label for="respondent">Full Name <span style="color: var(--danger, #ef4444);">*</span></label>
                            <input wire:model="respondent" type="text" id="respondent" placeholder="e.g. Maria Santos">
                            @error('respondent') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label for="respondent_phone">Contact Number</label>
                            <input wire:model="respondent_phone" type="tel" id="respondent_phone" maxlength="11" placeholder="e.g. 09XX XXX XXXX">
                            @error('respondent_phone') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="respondent_address">Address</label>
                        <input wire:model="respondent_address" type="text" id="respondent_address" placeholder="Purok / Street, Barangay Bula">
                        @error('respondent_address') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Case Details Row --}}
            <div class="form-row">
                <div class="form-section full-width">
                    <h3 class="section-title">CASE DETAILS</h3>
                    <div class="form-columns">
                        <div class="form-group">
                            <label for="case_number">Blotter ID <span style="color: var(--danger, #ef4444);">*</span></label>
                            <input wire:model="case_number" type="text" id="case_number" placeholder="e.g. BP-2026-001">
                            <small class="text-muted">Auto-suggested. You may override with a custom ID.</small>
                            @error('case_number') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label for="nature_of_case">Nature of Case <span style="color: var(--danger, #ef4444);">*</span></label>
                            <input wire:model="nature_of_case" type="text" id="nature_of_case" placeholder="e.g. Property Dispute, Collection of Debt">
                            <small class="text-muted">Enter the nature of the dispute (free text input)</small>
                            @error('nature_of_case') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-columns">
                        <div class="form-group">
                            <label for="filed_date">Date Filed <span style="color: var(--danger, #ef4444);">*</span></label>
                            <input wire:model="filed_date" type="date" id="filed_date">
                            @error('filed_date') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-columns">
                        <div class="form-group">
                            <label for="date_of_service_summon">Date of Service of Summon</label>
                            <input wire:model="date_of_service_summon" type="date" id="date_of_service_summon">
                            <small class="text-muted">When the summon was served to the respondent</small>
                            @error('date_of_service_summon') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input wire:model="remarks" type="text" id="remarks" placeholder="Additional notes or observations">
                            <small class="text-muted">Optional general remarks about the case</small>
                            @error('remarks') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description of Complaint</label>
                        <textarea wire:model="description" id="description" rows="4" placeholder="Briefly describe the nature of the dispute, relevant facts, and what resolution is being sought..."></textarea>
                        <small class="text-muted">Optional but recommended for DILG record completeness.</small>
                        @error('description') <p class="error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('cases.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <span wire:loading.remove wire:target="submit">
                        <i class="ph ph-check"></i> File Case
                    </span>
                    <span wire:loading wire:target="submit">
                        <i class="ph ph-spinner ph-spin"></i> Filing...
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
        .form-columns {
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
    </style>
</div>
