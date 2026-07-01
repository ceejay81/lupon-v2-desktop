<div>
    {{-- Search Bar --}}
    <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem;">
            Enter the citizen's full name to check if they have any case filed in the barangay.
            Results show cases where the citizen is either the <strong>complainant</strong> or the <strong>respondent</strong>.
        </p>

        <form wire:submit="search" style="display: flex; gap: 0.75rem; align-items: flex-start;">
            <div style="flex: 1; position: relative;">
                <i class="ph ph-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                <input
                    wire:model="searchName"
                    type="text"
                    placeholder="e.g. Juan Dela Cruz"
                    class="form-control"
                    style="padding-left: 2.5rem; font-size: 1rem;"
                    autofocus
                >
                @error('searchName')
                    <span style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                <span wire:loading.remove wire:target="search"><i class="ph ph-magnifying-glass"></i> Search</span>
                <span wire:loading wire:target="search"><i class="ph ph-spinner ph-spin"></i> Searching...</span>
            </button>

            @if($hasSearched)
                <button type="button" wire:click="clear" class="btn btn-secondary" style="white-space: nowrap;">
                    <i class="ph ph-x"></i> Clear
                </button>
            @endif
        </form>

        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.75rem;">
            💡 Tip: You can search by first name, last name, or full name. Partial matches work too.
        </p>
    </div>

    {{-- Results --}}
    @if($hasSearched)
        <div>
            @if($totalCases === 0)
                <div class="card" style="padding: 3rem; text-align: center;">
                    <i class="ph ph-folder-open" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem; display: block;"></i>
                    <p style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary);">No cases found</p>
                    <p style="color: var(--text-muted); margin-top: 0.5rem;">
                        No cases were found for "<strong>{{ $searchName }}</strong>" in the barangay records.
                    </p>
                </div>
            @else
                {{-- Summary Banner --}}
                <div class="card" style="padding: 1.25rem 1.5rem; margin-bottom: 1rem; border-left: 4px solid var(--accent-blue);">
                    <p style="font-weight: 600; font-size: 1rem; margin-bottom: 0.25rem;">
                        📋 Cases found for: <span style="color: var(--accent-blue);">{{ strtoupper($searchName) }}</span>
                    </p>
                    <div style="display: flex; gap: 1.5rem; font-size: 0.875rem; color: var(--text-muted);">
                        <span>Total: <strong style="color: var(--text-primary);">{{ $totalCases }}</strong></span>
                        <span>Active: <strong style="color: var(--accent-blue);">{{ $activeCases }}</strong></span>
                        <span>Resolved: <strong style="color: var(--success);">{{ $resolvedCases }}</strong></span>
                    </div>

                    @if($activeCases > 0)
                        @php
                            $nextHearing = collect($results)->whereNotNull('next_hearing')->sortBy('next_hearing.date')->first();
                        @endphp
                        @if($nextHearing)
                            <div style="margin-top: 0.75rem; padding: 0.5rem 0.75rem; background: var(--warning-light); border-radius: var(--radius-md); font-size: 0.875rem; color: var(--warning);">
                                ⚠️ Next action: Attend hearing on
                                <strong>{{ $nextHearing['next_hearing']['date'] }} at {{ $nextHearing['next_hearing']['time'] }}</strong>
                                — {{ $nextHearing['next_hearing']['location'] }}
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Case Cards --}}
                @foreach($results as $index => $result)
                    <div class="card" style="padding: 1.5rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div>
                                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Case #{{ $index + 1 }}</span>
                                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--accent-blue); margin-top: 0.25rem;">
                                    {{ $result['case_number'] }}
                                </h3>
                            </div>

                            @php
                                $badgeClass = match($result['status_code']) {
                                    'filed'              => 'badge-filed',
                                    'under_mediation'    => 'badge-mediation',
                                    'under_conciliation' => 'badge-conciliation',
                                    'under_arbitration'  => 'badge-arbitration',
                                    'settled'            => 'badge-settled',
                                    'certified_to_court' => 'badge-court',
                                    'dismissed'          => 'badge-dismissed',
                                    'withdrawal'         => 'badge-withdrawn',
                                    default              => 'badge-filed',
                                };
                            @endphp
                            <span class="status-badge {{ $badgeClass }}" style="font-size: 0.8rem;">
                                {{ $result['status'] }}
                            </span>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <p style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Role</p>
                                <p style="font-weight: 600; color: var(--text-primary);">{{ $result['role'] }}</p>
                            </div>
                            <div>
                                <p style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Opposing Party</p>
                                <p style="font-weight: 600; color: var(--text-primary);">{{ $result['opposing_party'] }}</p>
                            </div>
                            <div>
                                <p style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Nature of Case</p>
                                <p style="color: var(--text-secondary);">{{ $result['nature'] }}</p>
                            </div>
                            <div>
                                <p style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Date Filed</p>
                                <p style="color: var(--text-secondary);">{{ $result['filed_date'] }}</p>
                            </div>
                            @if($result['settled_date'])
                                <div>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Date Settled</p>
                                    <p style="color: var(--success); font-weight: 500;">{{ $result['settled_date'] }}</p>
                                </div>
                            @endif
                        </div>

                        @if($result['next_hearing'])
                            <div style="padding: 0.75rem 1rem; background: var(--info-light); border-radius: var(--radius-md); border-left: 3px solid var(--accent-blue); margin-bottom: 0.75rem;">
                                <p style="font-size: 0.875rem; font-weight: 600; color: var(--accent-blue); margin-bottom: 0.25rem;">
                                    📅 Next Hearing
                                </p>
                                <p style="font-size: 0.875rem; color: var(--accent-blue);">
                                    {{ $result['next_hearing']['date'] }} at {{ $result['next_hearing']['time'] }}
                                    &mdash; {{ $result['next_hearing']['type'] }}
                                    &mdash; {{ $result['next_hearing']['location'] }}
                                </p>
                            </div>
                        @elseif(in_array($result['status_code'], ['settled', 'dismissed', 'withdrawal']))
                            <div style="padding: 0.75rem 1rem; background: var(--success-light); border-radius: var(--radius-md); border-left: 3px solid var(--success); margin-bottom: 0.75rem;">
                                <p style="font-size: 0.875rem; color: var(--success);">
                                    ✅ This case has been resolved. No further action needed.
                                </p>
                            </div>
                        @endif

                        {{-- Staff-only: link to full case detail --}}
                        @auth
                            <div style="padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                                <a href="{{ route('cases.show', ['case' => $result['case_id']]) }}"
                                   style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; color: var(--accent-blue); text-decoration: none;">
                                    <i class="ph ph-arrow-square-out"></i> View Full Case Record
                                </a>
                            </div>
                        @endauth
                    </div>
                @endforeach

                {{-- Print Button --}}
                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="ph ph-printer"></i> Print This Page
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>

