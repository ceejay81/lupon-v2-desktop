<div class="dashboard-grid" style="padding: 0; gap: 2rem; display: grid; grid-template-columns: repeat(12, 1fr);">
    
    <!-- MAIN CONTENT (Left) -->
    <div style="grid-column: span 8; display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Schedule & Logistics Card -->
        <div class="card" style="padding: 0;">
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-hover);">
                <h3 style="font-size: 0.875rem; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="ph-bold ph-calendar-check" style="color: var(--accent-blue); font-size: 1.25rem;"></i>
                    Logistics & Schedule
                </h3>
            </div>
            <div style="padding: 2.5rem; display: grid; grid-template-columns: repeat(2, 1fr); gap: 3rem;">
                <div>
                    <label style="display: block; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">Scheduled Date & Time</label>
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 52px; height: 52px; background: var(--accent-light); color: var(--accent-blue); border-radius: 14px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <span style="font-size: 1.25rem; font-weight: 800; line-height: 1;">{{ $hearing->scheduled_at->format('d') }}</span>
                            <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase;">{{ $hearing->scheduled_at->format('M') }}</span>
                        </div>
                        <div>
                            <p style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary);">{{ $hearing->scheduled_at->format('F d, Y') }}</p>
                            <p style="font-size: 0.875rem; color: var(--text-secondary); font-weight: 500;">{{ $hearing->scheduled_at->format('h:i A') }} (PH Local Time)</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">Meeting Location</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 52px; height: 52px; background: var(--warning-light, rgba(245, 158, 11, 0.1)); color: var(--warning, #f59e0b); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="ph-fill ph-map-pin"></i>
                        </div>
                        <div>
                            <p style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary);">{{ $hearing->location }}</p>
                            <p style="font-size: 0.875rem; color: var(--text-secondary); font-weight: 500;">Official Barangay Session Hall</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance & Outcome Card -->
        <div class="card" style="padding: 0; border-top: 4px solid var(--success);">
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-hover); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 0.875rem; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="ph-bold ph-users-three" style="color: var(--success); font-size: 1.25rem;"></i>
                    Attendance & Outcome Record
                </h3>
            </div>
            <div style="padding: 2rem;">
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
                                    <p style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin: 0;">Complainant</p>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">{{ $hearing->luponCase->complainant }}</p>
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
                                    <p style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin: 0;">Respondent</p>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">{{ $hearing->luponCase->respondent }}</p>
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
                <div style="padding-top: 2rem; border-top: 2px solid var(--border-color); margin-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <div>
                            <h4 style="font-size: 0.875rem; font-weight: 800; color: var(--text-primary); margin: 0; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.75rem;">
                                <i class="ph ph-note-pencil" style="color: var(--accent-blue); font-size: 1.25rem;"></i>
                                Recording & Outcome
                            </h4>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1rem; background: var(--bg-hover); border: 1px solid var(--border-color); border-radius: 12px; font-size: 0.75rem; font-weight: 700; color: var(--text-secondary);">
                            <i class="ph-bold ph-pulse" style="color: var(--accent-blue);"></i> CURRENT HEARING STATUS:
                            <span style="color: var(--accent-blue); text-transform: uppercase;">{{ $hearing->status }}</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2.5rem;">
                        <div style="display: grid; grid-template-columns: 240px 1fr; gap: 2rem; align-items: flex-start;">
                            <div class="form-group">
                                <label style="display: block; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">Updated Session Status</label>
                                <div style="position: relative;">
                                    <select wire:model.live="hearing_status" class="form-control" style="font-weight: 700; height: 48px; border-radius: 12px; border: 2px solid var(--border-color); padding-left: 1rem; background: var(--bg-card); appearance: none;">
                                        <option value="scheduled">⏱ Scheduled</option>
                                        <option value="confirmed">📅 Confirmed</option>
                                        <option value="completed" {{ (!$complainant_attended || !$respondent_attended) ? 'disabled' : '' }}>✅ Completed</option>
                                        <option value="postponed">◴ Postponed</option>
                                        <option value="failed">✕ Failed</option>
                                        <option value="cancelled">🚫 Cancelled</option>
                                    </select>
                                    <i class="ph ph-caret-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--text-muted);"></i>
                                </div>
                                @error('hearing_status') <p style="color: var(--danger); font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem;">{{ $message }}</p> @enderror
                            </div>

                            @if($hearing_status === 'completed')
                                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; animation: fadeIn 0.3s ease-out;">
                                    <div class="form-group">
                                        <label style="display: block; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">Hearing Outcome <span style="color: var(--danger);">*</span></label>
                                        <input wire:model="outcome" type="text" class="form-control" style="height: 48px; border-radius: 12px; border: 2px solid var(--border-color); padding: 0 1rem; font-weight: 600;" placeholder="e.g. Agreement reached, Moved to Conciliation, Failed to appear...">
                                    </div>
                                    <div class="form-group">
                                        <label style="display: block; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">Session Minutes / Highlights</label>
                                        <textarea wire:model="minutes" class="form-control" rows="4" style="border-radius: 12px; border: 2px solid var(--border-color); padding: 1rem; font-weight: 500; line-height: 1.6;" placeholder="Briefly document the key points discussed and any conditions of the agreement..."></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                        <button wire:click="updateStatus" type="button" class="btn btn-primary" style="padding: 0 2.5rem; height: 52px; font-weight: 800; font-size: 0.9375rem; border-radius: 14px; box-shadow: 0 8px 16px rgba(59, 130, 246, 0.25); display: flex; align-items: center; gap: 0.75rem;">
                            <span wire:loading.remove wire:target="updateStatus"><i class="ph-bold ph-floppy-disk"></i> Update Process Record</span>
                            <span wire:loading wire:target="updateStatus"><i class="ph ph-spinner ph-spin"></i> Saving Results...</span>
                        </button>
                    </div>
                </div>

                {{-- Next Session Action --}}
                @php
                    $canSchedule = in_array($hearing->status, ['failed', 'cancelled', 'postponed']);
                    $isPending = in_array($hearing->status, ['scheduled', 'confirmed']);
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
                                    'failed' => ['Book a New Session', 'Previous session failed. Schedule a replacement hearing.'],
                                    'cancelled' => ['Book a New Session', 'Previous session was cancelled. Schedule a new date.'],
                                    'postponed' => ['Book Rescheduled Session', 'This hearing was postponed. Set a new date to continue.'],
                                    default => ['Schedule Next Hearing', 'Proceed with the next step in this case.'],
                                };
                            @endphp
                            <a href="{{ route('hearings.index', ['schedule_for' => $hearing->luponCase->id]) }}" style="display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem 1.25rem; background: var(--accent-blue); color: white; border-radius: var(--radius-md); font-size: 0.875rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(59,130,246,0.25); transition: filter 0.15s;" onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter='none'">
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
        </div>

    </div>

    <!-- SIDEBAR (Right) -->
    <div style="grid-column: span 4; display: flex; flex-direction: column; gap: 2rem;">

        <!-- Minutes & Notes (Moved) (Reactive Section) -->
        @if($outcome || $minutes)
            <div class="card" style="padding: 0; background-color: var(--bg-hover); border: 1px solid var(--border-color);">
                <div style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); background: var(--bg-card);">
                    <h3 style="font-size: 0.8125rem; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.625rem;">
                        <i class="ph ph-notebook-fill" style="color: var(--accent-blue); font-size: 1.125rem;"></i>
                        Resolution Record
                    </h3>
                </div>
                <div style="padding: 1.75rem;">
                    @if($outcome)
                        <div style="margin-bottom: 1.75rem;">
                            <label style="display: block; font-size: 0.625rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem; padding-left: 2px;">Hearing Outcome</label>
                            <div style="display: flex; align-items: flex-start; gap: 0.875rem; background: var(--bg-card); padding: 1rem; border-radius: 12px; border: 1px solid var(--success)20; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.03);">
                                <div style="width: 24px; height: 24px; background: var(--success-light); color: var(--success); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; flex-shrink: 0;">
                                    <i class="ph ph-check-square"></i>
                                </div>
                                <p style="font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.4;">{{ $outcome }}</p>
                            </div>
                        </div>
                    @endif

                    @if($minutes)
                        <div>
                            <label style="display: block; font-size: 0.625rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem; padding-left: 2px;">Session Minutes</label>
                            <div style="background: var(--bg-page); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); font-size: 0.8125rem; line-height: 1.6; color: var(--text-secondary); white-space: pre-wrap; font-style: italic;">{{ $minutes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Case Brief Sidebar Card -->
        <div class="card" style="padding: 0; background: var(--bg-card);">
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-hover);">
                <h3 style="font-size: 0.875rem; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="ph-bold ph-scales" style="color: var(--accent-blue); font-size: 1.25rem;"></i>
                    Case Brief
                </h3>
            </div>
            <div style="padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem;">
                <div>
                    <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Official Case ID</p>
                    <a href="{{ route('cases.show', $hearing->luponCase) }}" style="font-size: 1.25rem; font-weight: 800; color: var(--accent-blue); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                        {{ $hearing->luponCase->case_number }}
                        <i class="ph ph-arrow-square-out" style="font-size: 1rem;"></i>
                    </a>
                </div>
                
                <div>
                    <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Nature of Dispute</p>
                    <p style="font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); line-height: 1.4;">{{ $hearing->luponCase->nature_of_case }}</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1.25rem; background: var(--bg-hover); border-radius: 12px; border: 1px solid var(--border-color);">
                    <div>
                        <p style="font-size: 0.625rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Case Status</p>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--accent-blue);">{{ $hearing->luponCase->status_label }}</span>
                    </div>
                    <div>
                        <p style="font-size: 0.625rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Filed Date</p>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-primary);">{{ $hearing->luponCase->filed_date->format('M d, Y') }}</span>
                    </div>
                </div>

                <hr style="border: none; border-top: 1px solid var(--border-color);">

                <div>
                    <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Principal Parties</p>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--accent-light); color: var(--accent-blue); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">C</div>
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">{{ $hearing->luponCase->complainant }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">R</div>
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">{{ $hearing->luponCase->respondent }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card" style="padding: 1.5rem; background: var(--bg-hover); border: 1px solid var(--border-color);">
            <p style="font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph-bold ph-lightning" style="color: var(--warning);"></i> Quick Actions
            </p>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('cases.show', $hearing->luponCase) }}" class="btn-secondary" style="display: flex; align-items: center; gap: 0.75rem; height: 42px; text-decoration: none; font-size: 0.8125rem; justify-content: center;">
                    <i class="ph ph-folder-open"></i> Open Case Folder
                </a>
            </div>
        </div>

    </div>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</div>