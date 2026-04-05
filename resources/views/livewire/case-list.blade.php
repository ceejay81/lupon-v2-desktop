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
                <option value="under_mediation">Mediation</option>
                <option value="under_conciliation">Conciliation</option>
                <option value="under_arbitration">Arbitration</option>
                <option value="settled">Settled</option>
                <option value="certified_to_court">Certified</option>
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
                    @php
                        $docsPercent = $docsCompleteness[$case->id] ?? 0;
                        $docsClass   = $docsPercent === 100 ? 'docs-complete' : ($docsPercent > 0 ? 'docs-incomplete' : '');
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('cases.show', $case) }}"
                               style="font-weight: 700; color: var(--accent-blue); text-decoration: none;">
                                {{ $case->case_number }}
                            </a>
                        </td>
                        <td style="max-width: 160px;">
                            <span style="font-weight: 500;">{{ Str::limit($case->complainant, 30) }}</span>
                            @if($case->complainant_address)
                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ Str::limit($case->complainant_address, 35) }}</div>
                            @endif
                        </td>
                        <td style="max-width: 160px;">
                            <span style="font-weight: 500;">{{ Str::limit($case->respondent, 30) }}</span>
                            @if($case->respondent_address)
                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ Str::limit($case->respondent_address, 35) }}</div>
                            @endif
                        </td>
                        <td style="font-size: 0.8rem;">
                            {{ $case->nature_of_case }}
                        </td>
                        <td>
                            <span class="status-badge {{ $case->status_badge_class }}">
                                {{ $case->status_label }}
                            </span>
                        </td>
                        <td style="font-size: 0.8rem; white-space: nowrap;">
                            {{ $case->filed_date->format('M d, Y') }}
                        </td>
                        <td style="text-align: center;">
                            @php $percent = $case->docs_completeness; @endphp
                            @if($percent === 100)
                                <div style="display: inline-flex; align-items: center; gap: 0.5rem; color: #10b981; font-weight: 700; font-size: 0.9rem;">
                                    <i class="ph ph-check-square" style="font-size: 1.25rem;"></i> 100%
                                </div>
                            @else
                                <div style="display: inline-flex; align-items: center; gap: 0.5rem; color: #f59e0b; font-weight: 700; font-size: 0.9rem;">
                                    <i class="ph ph-warning" style="font-size: 1.25rem;"></i> {{ $percent }}%
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
                {{ $cases->links() }}
            </div>
        @endif
    </div>
</section>
