<div>
    @if($currentPangkat && !$isAssigning)
        <div style="background: var(--bg-hover); padding: 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph ph-users-three" style="color: var(--accent-blue);"></i>
                Assigned Panel Members
            </div>
            
            <div style="display: grid; gap: 0.5rem;">
                @foreach($currentPangkat->members as $index => $member)
                    <div style="display: flex; gap: 0.5rem; align-items: center; padding: 0.5rem; background: var(--bg-primary); border-radius: 0.25rem; border: 1px solid var(--border-color);">
                        <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--accent-blue); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.625rem; font-weight: bold;">
                            {{ $index + 1 }}
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; font-size: 0.875rem; color: var(--text-primary);">{{ $member->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $member->position }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--text-muted);">
                Assigned on {{ $currentPangkat->assigned_at->format('M d, Y') }}
            </div>
        </div>
        
        <button wire:click="$set('isAssigning', true)" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;"><i class="ph ph-arrows-clockwise"></i> Re-assign Panel</button>
    @endif

    @if($isAssigning)
        <form wire:submit="assignPangkat" style="margin-top: 1rem;">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Select Pangkat Members</label>
                
                <div style="max-height: 300px; overflow-y: auto; background: var(--bg-hover); border: 1px solid var(--border-color); border-radius: 0.375rem; padding: 0.5rem;">
                    @foreach($availableMembers as $member)
                        <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; cursor: pointer; border-bottom: 1px solid var(--border-color); last-child: border-bottom-0;">
                            <input type="checkbox" wire:model.live="selectedMemberIds" value="{{ $member->id }}" style="width: 1.125rem; height: 1.125rem;">
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">{{ $member->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $member->position }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
                
                @error('selectedMemberIds') <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
                @error('selectedMemberIds.*') <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="background: var(--bg-hover); padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.875rem;">
                <span style="font-weight: 600; color: var(--text-primary);">Total Selected:</span> 
                <span style="color: var(--accent-blue); font-weight: bold;">{{ count($selectedMemberIds) }}</span>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.5rem;" @if(count($selectedMemberIds) === 0) disabled @endif>
                <span wire:loading.remove wire:target="assignPangkat">Confirm Assignment</span>
                <span wire:loading wire:target="assignPangkat"><i class="ph ph-spinner ph-spin"></i> Saving...</span>
            </button>
        </form>
    @endif
</div>
