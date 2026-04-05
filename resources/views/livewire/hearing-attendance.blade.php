<div>



    {{-- Parties Attendance Section --}}
    <div style="margin-bottom: 2rem;">
        <h4 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted, #94a3b8); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-users-three"></i> Party Attendance
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">

            <label style="display: flex; flex-direction: column; gap: 0.75rem; cursor: pointer; padding: 1.25rem; border: 2px solid {{ $complainant_attended ? 'var(--accent-blue)' : 'var(--border-color)' }}; border-radius: 12px; background: {{ $complainant_attended ? 'var(--accent-light)' : 'var(--bg-card)' }}; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;">
                @if($complainant_attended)
                    <div style="position: absolute; top: 0; right: 0; padding: 4px 8px; background: var(--accent-blue, #3b82f6); color: white; border-bottom-left-radius: 8px;">
                        <i class="ph ph-check" style="font-size: 0.8rem;"></i>
                    </div>
                @endif
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; background: {{ $complainant_attended ? 'var(--bg-card)' : 'var(--bg-hover)' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: {{ $complainant_attended ? 'var(--accent-blue)' : 'var(--text-muted)' }}; border: 1px solid var(--border-color);">
                        <i class="ph ph-user-circle" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary, #1e293b); margin: 0;">Complainant</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted, #94a3b8); margin: 0;">{{ $hearing->luponCase->complainant }}</p>
                    </div>
                </div>
                <input type="checkbox" wire:click="toggleComplainant" @checked($complainant_attended) style="display: none;">
                <div style="font-size: 0.75rem; font-weight: 600; text-align: center; padding-top: 0.5rem; color: {{ $complainant_attended ? 'var(--accent-blue)' : 'var(--text-muted)' }}; border-top: 1px dashed var(--border-color);">
                    {{ $complainant_attended ? 'MARKED AS PRESENT' : 'MARK AS PRESENT' }}
                </div>
            </label>

            <label style="display: flex; flex-direction: column; gap: 0.75rem; cursor: pointer; padding: 1.25rem; border: 2px solid {{ $respondent_attended ? 'var(--danger)' : 'var(--border-color)' }}; border-radius: 12px; background: {{ $respondent_attended ? 'var(--danger-light)' : 'var(--bg-card)' }}; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;">
                @if($respondent_attended)
                    <div style="position: absolute; top: 0; right: 0; padding: 4px 8px; background: var(--danger, #ef4444); color: white; border-bottom-left-radius: 8px;">
                        <i class="ph ph-check" style="font-size: 0.8rem;"></i>
                    </div>
                @endif
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; background: {{ $respondent_attended ? 'var(--bg-card)' : 'var(--bg-hover)' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: {{ $respondent_attended ? 'var(--danger)' : 'var(--text-muted)' }}; border: 1px solid var(--border-color);">
                        <i class="ph ph-user-circle-minus" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary, #1e293b); margin: 0;">Respondent</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted, #94a3b8); margin: 0;">{{ $hearing->luponCase->respondent }}</p>
                    </div>
                </div>
                <input type="checkbox" wire:click="toggleRespondent" @checked($respondent_attended) style="display: none;">
                <div style="font-size: 0.75rem; font-weight: 600; text-align: center; padding-top: 0.5rem; color: {{ $respondent_attended ? 'var(--danger)' : 'var(--text-muted)' }}; border-top: 1px dashed var(--border-color);">
                    {{ $respondent_attended ? 'MARKED AS PRESENT' : 'MARK AS PRESENT' }}
                </div>
            </label>

        </div>
    </div>

    {{-- Lupon Members Section --}}
    <div style="margin-bottom: 2rem; padding: 1.5rem; background: var(--bg-hover); border-radius: 12px; border: 1px solid var(--border-color);">
        <h4 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted, #94a3b8); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-scales"></i> Lupon Members Present
        </h4>

        @php $memberAttendances = $hearing->attendances->where('party_type', 'lupon_member'); @endphp

        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem;">
            @forelse($memberAttendances as $att)
                <div style="display: inline-flex; align-items: center; gap: 0.625rem; padding: 0.5rem 1rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; color: var(--text-secondary); box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <div style="width: 1.25rem; height: 1.25rem; background: var(--accent-blue, #3b82f6); border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.65rem;">
                        <i class="ph ph-user-check"></i>
                    </div>
                    {{ $att->luponMember->name ?? $att->name }}
                    <button wire:click="removeMember({{ $att->id }})" type="button" style="background: none; border: none; cursor: pointer; color: var(--text-muted, #94a3b8); padding: 0; line-height: 1; transition: color 0.2s;" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'">
                        <i class="ph ph-x-circle" style="font-size: 1.1rem;"></i>
                    </button>
                </div>
            @empty
                <div style="width: 100%; padding: 1.5rem; text-align: center; background: var(--bg-card); border: 1px dashed var(--border-color); border-radius: 8px; color: var(--text-muted); font-size: 0.8125rem;">
                    <i class="ph ph-user-plus" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                    No Lupon members recorded for this session.
                </div>
            @endforelse
        </div>

        <form wire:submit="addMember" style="display: flex; gap: 0.75rem;">
            <div style="position: relative; flex: 1;">
                <i class="ph ph-plus-circle" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted, #94a3b8);"></i>
                <select wire:model="member_id" class="form-control" style="padding-left: 2.25rem;">
                    <option value="">Select member to add...</option>
                    @foreach($luponMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0 1.5rem;">
                <span wire:loading.remove wire:target="addMember"><i class="ph ph-plus-bold"></i> Add Member</span>
                <span wire:loading wire:target="addMember"><i class="ph ph-spinner ph-spin"></i> Adding...</span>
            </button>
        </form>
    </div>

    {{-- Session Outcome & Status Section --}}
    <div style="padding-top: 1.5rem; border-top: 2px solid var(--border-color);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin: 0;">Recording & Outcome</h4>
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: var(--bg-hover); border-radius: 8px; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">
                <i class="ph ph-pulse" style="color: var(--accent-blue);"></i> Current Status: {{ strtoupper($hearing->status) }}
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 1.5rem; align-items: flex-start;">
                <div class="form-group">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary, #64748b);">Updated Status</label>
                    <select wire:model.live="hearing_status" class="form-control" style="font-weight: 600;">
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed" {{ (! $complainant_attended || ! $respondent_attended) ? 'disabled' : '' }}>✓ Completed {{ (! $complainant_attended || ! $respondent_attended) ? ' (Both parties must be present)' : '' }}</option>
                        <option value="postponed">◴ Postponed</option>
                        <option value="failed">✕ Failed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('hearing_status') <p style="color: var(--danger); font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem;">{{ $message }}</p> @enderror
                </div>

                @if($hearing_status === 'completed')
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; animation: fadeIn 0.3s ease-out;">
                        <div class="form-group">
                            <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary, #64748b);">Hearing Outcome <span style="color: var(--danger);">*</span></label>
                            <input wire:model="outcome" type="text" class="form-control" placeholder="e.g. Agreement reached, Moved to Conciliation, Failed to appear...">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary, #64748b);">Session Minutes / Highlights</label>
                            <textarea wire:model="minutes" class="form-control" rows="3" placeholder="Briefly document the key points discussed and any conditions of the agreement..."></textarea>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; align-items: center; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
            <button wire:click="updateStatus" type="button" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 700; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
                <span wire:loading.remove wire:target="updateStatus"><i class="ph ph-floppy-disk"></i> Update Process Record</span>
                <span wire:loading wire:target="updateStatus"><i class="ph ph-spinner ph-spin"></i> Saving Results...</span>
            </button>
        </div>
    </div>

    {{-- Strategic Next Steps (Post-Completion) --}}
    @if($hearing->status === 'completed' && $complainant_attended && $respondent_attended)
        <div style="margin-top: 2rem; padding: 1.5rem; background: var(--bg-hover); border: 1px solid var(--border-color); border-radius: 12px; animation: slideIn 0.4s ease-out;">
            <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="width: 2rem; height: 2rem; background: var(--accent-blue); border-radius: 8px; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="ph ph-rocket-launch"></i>
                </div>
                <div>
                    <h5 style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin: 0;">Session Resolved: What's the Next Step?</h5>
                    <p style="font-size: 0.8125rem; color: var(--accent-blue); margin-top: 0.25rem;">Based on the result of this hearing, pick the appropriate legal action to advance the case.</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                {{-- 1. Settle Case — always available --}}
                <button wire:click="settleCase" style="display: flex; flex-direction: column; gap: 0.5rem; padding: 1rem; background: var(--bg-card); border-radius: 8px; border: 1px solid var(--border-color); text-align: left; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='var(--success)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <span wire:loading.remove wire:target="settleCase"><i class="ph ph-handshake" style="color: var(--success);"></i> 1. Settle Case</span>
                        <span wire:loading wire:target="settleCase"><i class="ph ph-spinner ph-spin"></i> Processing...</span>
                    </span>
                    <p style="font-size: 0.6875rem; color: var(--text-muted); margin: 0;">Agreement was reached. Close the case as Settled.</p>
                </button>

                {{-- 2. Move to Conciliation — only if still in mediation --}}
                @if($hearing->luponCase->status === 'under_mediation')
                <button wire:click="moveToConciliation" style="display: flex; flex-direction: column; gap: 0.5rem; padding: 1rem; background: var(--bg-card); border-radius: 8px; border: 1px solid var(--border-color); text-align: left; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='var(--accent-blue)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <span wire:loading.remove wire:target="moveToConciliation"><i class="ph ph-users-three" style="color: var(--accent-blue);"></i> 2. Move to Conciliation</span>
                        <span wire:loading wire:target="moveToConciliation"><i class="ph ph-spinner ph-spin"></i> Moving...</span>
                    </span>
                    <p style="font-size: 0.6875rem; color: var(--text-muted); margin: 0;">Mediation failed. Escalate to Pangkat stage.</p>
                </button>
                @endif

                {{-- 3. Move to Arbitration — only if in conciliation and both parties consent --}}
                @if($hearing->luponCase->status === 'under_conciliation')
                <button wire:click="moveToArbitration" style="display: flex; flex-direction: column; gap: 0.5rem; padding: 1rem; background: var(--bg-card); border-radius: 8px; border: 1px solid var(--border-color); text-align: left; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='var(--warning)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <span wire:loading.remove wire:target="moveToArbitration"><i class="ph ph-scales" style="color: var(--warning);"></i> 3. Move to Arbitration</span>
                        <span wire:loading wire:target="moveToArbitration"><i class="ph ph-spinner ph-spin"></i> Moving...</span>
                    </span>
                    <p style="font-size: 0.6875rem; color: var(--text-muted); margin: 0;">Conciliation failed. Both parties consent to Pangkat decision.</p>
                </button>
                @endif

                {{-- 4. Issue Certification — only if conciliation or arbitration stage --}}
                @if(in_array($hearing->luponCase->status, ['under_conciliation', 'under_arbitration']))
                <button wire:click="certifyToCourt" style="display: flex; flex-direction: column; gap: 0.5rem; padding: 1rem; background: var(--bg-card); border-radius: 8px; border: 1px solid var(--border-color); text-align: left; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='var(--danger)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <span wire:loading.remove wire:target="certifyToCourt"><i class="ph ph-certificate" style="color: var(--danger);"></i> {{ $hearing->luponCase->status === 'under_arbitration' ? '4.' : '3.' }} Issue Certification</span>
                        <span wire:loading wire:target="certifyToCourt"><i class="ph ph-spinner ph-spin"></i> Preparing...</span>
                    </span>
                    <p style="font-size: 0.6875rem; color: var(--text-muted); margin: 0;">All barangay attempts exhausted. Certify for court filing.</p>
                </button>
                @endif
            </div>
        </div>
    @endif

    <style>
        @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>

    {{-- Next Session Action --}}
    @php
        $canSchedule = in_array($hearing->status, ['failed', 'cancelled', 'postponed']);
        $isPending   = in_array($hearing->status, ['scheduled', 'confirmed']);
    @endphp

    @if($isPending || $canSchedule)
        <div style="margin-top: 1.5rem; padding: 1.25rem 1.5rem; background: var(--bg-hover); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
            <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph-bold ph-calendar-dots" style="color: var(--accent-blue);"></i> Follow-up Session
            </p>

            @if($isPending)
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--accent-light); border: 1px solid rgba(59,130,246,0.2); border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 600; color: var(--accent-blue);">
                    <i class="ph ph-clock" style="font-size: 1.1rem;"></i>
                    <div>
                        <div>Session already scheduled</div>
                        <div style="font-size: 0.72rem; font-weight: 500; color: var(--text-secondary); margin-top: 1px;">Complete or update this hearing first before scheduling another.</div>
                    </div>
                </div>
            @elseif($hasActiveHearing)
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--warning-light); border: 1px solid rgba(245,158,11,0.2); border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 600; color: var(--warning);">
                    <i class="ph ph-warning" style="font-size: 1.1rem;"></i>
                    <div>
                        <div>Another hearing is already active</div>
                        <div style="font-size: 0.72rem; font-weight: 500; color: var(--text-secondary); margin-top: 1px;">Resolve the existing scheduled hearing before adding a new one.</div>
                    </div>
                </div>
            @else
                @php
                    [$label, $description] = match($hearing->status) {
                        'failed'    => ['Book a New Session', 'Previous session failed. Schedule a replacement hearing.'],
                        'cancelled' => ['Book a New Session', 'Previous session was cancelled. Schedule a new date.'],
                        'postponed' => ['Book Rescheduled Session', 'This hearing was postponed. Set a new date to continue.'],
                        default     => ['Schedule Next Hearing', 'Proceed with the next step in this case.'],
                    };
                @endphp
                <a href="{{ route('hearings.index', ['schedule_for' => $hearing->luponCase->id]) }}"
                   style="display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem 1.25rem; background: var(--accent-blue); color: white; border-radius: var(--radius-md); font-size: 0.875rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(59,130,246,0.25); transition: filter 0.15s;"
                   onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter='none'">
                    <i class="ph ph-calendar-plus" style="font-size: 1.25rem; flex-shrink: 0;"></i>
                    <div>
                        <div>{{ $label }}</div>
                        <div style="font-size: 0.72rem; font-weight: 500; opacity: 0.85; margin-top: 1px;">{{ $description }}</div>
                    </div>
                </a>
            @endif
        </div>
    @endif
</div>

