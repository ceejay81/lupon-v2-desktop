<div>



    @if($currentPangkat)
        <div style="background: var(--bg-hover); padding: 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
                <i class="ph ph-gavel" style="color: var(--accent-blue);"></i>
                <span style="font-weight: 500; font-size: 0.875rem; color: var(--text-primary);">Chairperson:</span>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">{{ $currentPangkat->chairperson->name }}</span>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
                <i class="ph ph-pencil-simple" style="color: var(--accent-blue);"></i>
                <span style="font-weight: 500; font-size: 0.875rem; color: var(--text-primary);">Secretary:</span>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">{{ $currentPangkat->secretary->name }}</span>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <i class="ph ph-user" style="color: var(--accent-blue);"></i>
                <span style="font-weight: 500; font-size: 0.875rem; color: var(--text-primary);">Member:</span>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">{{ $currentPangkat->member->name }}</span>
            </div>
            <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--text-muted);">
                Assigned on {{ $currentPangkat->assigned_at->format('M d, Y') }}
            </div>
        </div>
        
        <button wire:click="$set('chairperson_id', '')" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;"><i class="ph ph-arrows-clockwise"></i> Re-assign Pangkat</button>
    @endif

    @if(!$currentPangkat || empty($chairperson_id))
        <form wire:submit="assignPangkat" style="margin-top: 1rem;">
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1rem;">
                
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Chairperson</label>
                    <select wire:model="chairperson_id" class="form-control" style="width: 100%;">
                        <option value="">-- Select Member --</option>
                        @foreach($availableMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->position }})</option>
                        @endforeach
                    </select>
                    @error('chairperson_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">Secretary</label>
                    <select wire:model="secretary_id" class="form-control" style="width: 100%;">
                        <option value="">-- Select Member --</option>
                        @foreach($availableMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->position }})</option>
                        @endforeach
                    </select>
                    @error('secretary_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">Member</label>
                    <select wire:model="member_id" class="form-control" style="width: 100%;">
                        <option value="">-- Select Member --</option>
                        @foreach($availableMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->position }})</option>
                        @endforeach
                    </select>
                    @error('member_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>

            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.5rem;">
                <span wire:loading.remove wire:target="assignPangkat">Assign</span>
                <span wire:loading wire:target="assignPangkat"><i class="ph ph-spinner ph-spin"></i> Saving...</span>
            </button>
        </form>
    @endif
</div>
