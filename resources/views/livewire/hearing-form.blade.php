<div>
    {{-- Modal Overlay --}}
    @if($showModal)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 500; display: flex; align-items: center; justify-content: center; padding: 2rem;" x-data x-transition>
            <div class="card" style="width: 100%; max-width: 560px; max-height: calc(100vh - 4rem); display: flex; flex-direction: column; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; animation: modal-slide-up 0.3s ease-out;">

                {{-- Modal Header --}}
                <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-card); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.125rem;">
                            {{ $hearingId ? 'Update Hearing Details' : 'Schedule New Hearing' }}
                        </h3>
                        <p style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                            {{ $hearingId ? 'Modify existing session' : 'Initialize a new mediation session' }}
                        </p>
                    </div>
                    <button wire:click="closeModal" type="button" style="width: 32px; height: 32px; border-radius: 8px; background: var(--bg-page); border: 1px solid var(--border-color); color: var(--text-muted); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--danger)'; this.style.color='var(--danger)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--text-muted)'">
                        <i class="ph ph-x" style="font-size: 1rem;"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div style="padding: 1.5rem 2rem; display: flex; flex-direction: column; gap: 1.25rem; overflow-y: auto; flex: 1;">

                    {{-- Case selector --}}
                    @if(!$case_id || $hearingId)
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Target Case Folder <span style="color: var(--danger);">*</span></label>
                            <select wire:model="case_id" class="search-input" style="width: 100%; height: 46px; font-weight: 500;">
                                <option value="">— Select an active case —</option>
                                @foreach($cases as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->case_number }} — {{ $c->complainant }} vs {{ $c->respondent }}
                                    </option>
                                @endforeach
                            </select>
                            @error('case_id') <p style="color: var(--danger); font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.375rem;"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Category <span style="color: var(--danger);">*</span></label>
                            <select wire:model="hearing_type" class="search-input" style="width: 100%; height: 46px; font-weight: 500;">
                                <option value="">— Select type —</option>
                                @foreach(\App\Livewire\HearingForm::TYPES as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('hearing_type') <p style="color: var(--danger); font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.375rem;"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Date & Time <span style="color: var(--danger);">*</span></label>
                            <input wire:model="scheduled_at" type="datetime-local" class="search-input" style="width: 100%; height: 46px; font-weight: 500;">
                            @error('scheduled_at') <p style="color: var(--danger); font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.375rem;"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Location</label>
                        <div style="position: relative;">
                            <i class="ph ph-map-pin" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                            <input wire:model="location" type="text" class="search-input" style="width: 100%; height: 46px; padding-left: 2.75rem; font-weight: 500;" placeholder="e.g. Barangay Hall Session Room">
                        </div>
                        @error('location') <p style="color: var(--danger); font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.375rem;"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </div>



                </div>

                {{-- Modal Footer --}}
                <div style="padding: 1rem 2rem; border-top: 1px solid var(--border-color); background: var(--bg-card); display: flex; justify-content: flex-end; gap: 1rem; flex-shrink: 0;">
                    <button wire:click="closeModal" type="button" class="btn-secondary" style="height: 46px; padding: 0 1.5rem;">
                        Cancel
                    </button>
                    <button wire:click="save" type="button" class="btn-primary" style="height: 46px; padding: 0 2rem; display: flex; align-items: center; gap: 0.75rem;">
                        <span wire:loading.remove wire:target="save">
                            <i class="ph-bold ph-calendar-check"></i> {{ $hearingId ? 'Confirm Updates' : 'Schedule Session' }}
                        </span>
                        <span wire:loading wire:target="save">
                            <i class="ph ph-spinner ph-spin"></i> Processing...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    <style>
        @keyframes modal-slide-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</div>
