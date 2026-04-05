<div style="position: relative;">

    {{-- Search Input --}}
    <div style="position: relative;">
        <i class="ph ph-magnifying-glass"
           style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem; pointer-events: none;"></i>
        <input
            wire:model.live.debounce.300ms="search"
            wire:blur="clearDropdown"
            type="text"
            class="form-control"
            placeholder="Search citizen records..."
            style="padding-left: 2.1rem;"
            autocomplete="off"
        >
        @if($search)
            <button wire:click="$set('search', '')" type="button"
                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1rem; padding: 0; line-height: 1;">
                <i class="ph ph-x-circle"></i>
            </button>
        @endif
    </div>

    {{-- Results Dropdown --}}
    @if($showDropdown)
        <div style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-md); z-index: 200; overflow: hidden;">

            @forelse($results as $index => $citizen)
                <button
                    wire:click="selectCitizen({{ $index }})"
                    wire:mousedown.prevent
                    type="button"
                    style="display: flex; align-items: center; gap: 0.75rem; width: 100%; padding: 0.625rem 0.875rem; background: none; border: none; border-bottom: 1px solid var(--border-color); cursor: pointer; text-align: left; transition: background 0.1s;"
                    onmouseover="this.style.background='var(--bg-hover)'"
                    onmouseout="this.style.background='none'"
                >
                    <i class="ph ph-user-circle" style="font-size: 1.25rem; color: var(--text-muted); flex-shrink: 0;"></i>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $citizen['name'] }}
                        </p>
                        @if($citizen['purok'] || $citizen['address'] || $citizen['phone'])
                            <p style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                @if($citizen['purok'])<i class="ph ph-map-pin"></i> {{ $citizen['purok'] }}@endif
                                @if($citizen['address']) · {{ Str::limit($citizen['address'], 30) }}@endif
                                @if($citizen['phone']) · <i class="ph ph-phone"></i> {{ $citizen['phone'] }}@endif
                            </p>
                        @endif
                    </div>
                    <i class="ph ph-arrow-bend-down-left" style="font-size: 0.8rem; color: var(--text-muted); flex-shrink: 0;"></i>
                </button>
            @empty
                <div style="padding: 0.75rem 0.875rem; font-size: 0.8rem; color: var(--text-muted);">
                    <i class="ph ph-magnifying-glass"></i> No citizen found for "<strong>{{ $search }}</strong>"
                </div>
            @endforelse

            {{-- Add new citizen button --}}
            <button
                wire:click="openCreateForm"
                wire:mousedown.prevent
                type="button"
                style="display: flex; align-items: center; gap: 0.5rem; width: 100%; padding: 0.6rem 0.875rem; background: var(--accent-light); border: none; cursor: pointer; font-size: 0.78rem; font-weight: 600; color: var(--accent-blue); text-align: left;"
                onmouseover="this.style.background='var(--bg-hover)'"
                onmouseout="this.style.background='var(--accent-light)'"
            >
                <i class="ph ph-user-plus"></i> Register "{{ $search }}" as new citizen
            </button>
        </div>
    @endif

    {{-- Inline Create Form --}}
    @if($showCreateForm)
        <div style="margin-top: 0.75rem; padding: 1rem; background: var(--accent-light); border: 1px solid var(--accent-blue); border-radius: var(--radius-md);">
            <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--accent-blue); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="ph ph-user-plus"></i> Register New Citizen
            </p>
            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                <div>
                    <label class="form-label">Full Name <span style="color: var(--danger);">*</span></label>
                    <input wire:model="newName" type="text" class="form-control" placeholder="e.g. Juan Dela Cruz">
                    @error('newName') <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem;">
                    <div>
                        <label class="form-label">Purok</label>
                        <input wire:model="newPurok" type="text" class="form-control" placeholder="e.g. Purok 3">
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input wire:model="newPhone" type="tel" class="form-control" maxlength="11" placeholder="09XX XXX XXXX">
                    </div>
                </div>
                <div>
                    <label class="form-label">Address</label>
                    <input wire:model="newAddress" type="text" class="form-control" placeholder="Street, Barangay Bula">
                </div>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.25rem;">
                    <button wire:click="cancelCreate" type="button" class="btn"
                        style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary); font-size: 0.78rem; padding: 0.4rem 0.875rem;">
                        Cancel
                    </button>
                    <button wire:click="createCitizen" type="button" class="btn btn-primary"
                        style="font-size: 0.78rem; padding: 0.4rem 0.875rem;">
                        <span wire:loading.remove wire:target="createCitizen"><i class="ph ph-check"></i> Save & Select</span>
                        <span wire:loading wire:target="createCitizen"><i class="ph ph-spinner ph-spin"></i> Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

