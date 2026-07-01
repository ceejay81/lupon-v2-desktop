<section class="card" id="search">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-light); background: var(--bg-card);">
        <h2 class="card-title" style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            🔍 Searchable Case Database (MOV #1)
        </h2>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <div style="position: relative; width: 340px;">
                <i class="ph ph-magnifying-glass" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1rem; pointer-events: none;"></i>
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search by case number, name, or keyword..."
                    class="form-control"
                    style="padding-left: 2.25rem; font-size: 0.85rem; height: 38px;">
            </div>
            
            <select wire:model.live="status" class="form-control" style="width: 160px; font-size: 0.85rem; height: 38px;">
                <option value="">All Statuses</option>
                <option value="filed">Filed</option>
                <option value="under_arbitration">Arbitration</option>
                <option value="settled">Settled</option>
                <option value="certified_to_court">Certified</option>
                <option value="dismissed">Dismissed</option>
                <option value="withdrawal">Withdrawn</option>
            </select>


            
            <a href="{{ route('cases.create') }}" class="btn" style="background: var(--accent-blue, #3b82f6); border: 1px solid var(--accent-blue, #3b82f6); color: white; padding: 0.5rem 1.25rem; font-size: 0.875rem; font-weight: 600; height: 38px; border-radius: var(--radius-md); text-decoration: none; display: inline-flex; align-items: center;">
                <i class="ph ph-plus" style="margin-right: 6px;"></i> File New Case
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="card" style="overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Case No.</th>
                    <th>Complainant</th>
                    <th>Respondent</th>
                    <th>Nature</th>
                    <th>Status</th>
                    <th>Filed</th>
                    <th style="text-align: center;">Docs</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cases as $case)
                    <tr>
                        <td>
                            <a href="{{ route('cases.show', $case) }}"
                               style="font-weight: 700; color: var(--accent-blue); text-decoration: none;">
                                {{ $case->case_number }}
                            </a>
                        </td>
                        <td style="max-width: 160px;">
                            <span style="font-weight: 500;">{{ Str::limit($case->complainants->pluck('name')->join(', ') ?: $case->complainant, 30) }}</span>
                            @if($case->complainants->isNotEmpty() && $case->complainants->first()->address)
                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ Str::limit($case->complainants->first()->address, 35) }}</div>
                            @elseif($case->complainant_address)
                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ Str::limit($case->complainant_address, 35) }}</div>
                            @endif
                        </td>
                        <td style="max-width: 160px;">
                            <span style="font-weight: 500;">{{ Str::limit($case->respondents->pluck('name')->join(', ') ?: $case->respondent, 30) }}</span>
                            @if($case->respondents->isNotEmpty() && $case->respondents->first()->address)
                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ Str::limit($case->respondents->first()->address, 35) }}</div>
                            @elseif($case->respondent_address)
                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ Str::limit($case->respondent_address, 35) }}</div>
                            @endif
                        </td>
                        <td style="font-size: 0.8rem;">
                            {{ $case->nature_of_case }}
                        </td>
                        <td>
                            <div style="position: relative; display: inline-block;">
                                <select wire:change="updateCaseStatus({{ $case->id }}, $event.target.value)" class="status-badge {{ $case->status_badge_class }}" style="border: none; cursor: pointer; outline: none; appearance: none; padding-right: 1.25rem;">
                                    <option value="filed" {{ $case->status == 'filed' ? 'selected' : '' }} style="color: #000; background: #fff;">Filed</option>
                                    <option value="under_arbitration" {{ $case->status == 'under_arbitration' ? 'selected' : '' }} style="color: #000; background: #fff;">Arbitration</option>
                                    <option value="settled" {{ $case->status == 'settled' ? 'selected' : '' }} style="color: #000; background: #fff;">Settled</option>
                                    <option value="certified_to_court" {{ $case->status == 'certified_to_court' ? 'selected' : '' }} style="color: #000; background: #fff;">Certified to Court</option>
                                    <option value="dismissed" {{ $case->status == 'dismissed' ? 'selected' : '' }} style="color: #000; background: #fff;">Dismissed</option>
                                    <option value="withdrawal" {{ $case->status == 'withdrawal' ? 'selected' : '' }} style="color: #000; background: #fff;">Withdrawn</option>
                                </select>
                                <i class="ph ph-caret-down" style="position: absolute; right: 0.35rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; pointer-events: none; opacity: 0.7;"></i>
                            </div>
                        </td>
                        <td style="font-size: 0.8rem; white-space: nowrap;">
                            {{ $case->filed_date->format('M d, Y') }}
                        </td>
                        <td style="text-align: center;">
                            @if($case->documents_count > 0)
                                <div style="display: inline-flex; align-items: center; gap: 0.25rem; color: #10b981; font-weight: 600; font-size: 0.9rem;">
                                    <i class="ph ph-files" style="font-size: 1rem;"></i> {{ $case->documents_count }}
                                </div>
                            @else
                                <div style="display: inline-flex; align-items: center; gap: 0.25rem; color: var(--text-muted); font-weight: 500; font-size: 0.9rem;">
                                    <i class="ph ph-files" style="font-size: 1rem;"></i> 0
                                </div>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('cases.show', $case) }}"
                               style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); text-decoration: none; background: var(--bg-card); transition: all 0.15s;"
                               onmouseover="this.style.borderColor='var(--accent-blue)';this.style.color='var(--accent-blue)'"
                               onmouseout="this.style.borderColor='var(--border-light)';this.style.color='var(--text-secondary)'">
                                <i class="ph ph-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            <i class="ph ph-folder-open" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            <p style="font-weight: 500;">No cases found.</p>
                            <p style="font-size: 0.8rem; margin-top: 0.25rem;">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($cases->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-light);">
                {{ $cases->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</section>
