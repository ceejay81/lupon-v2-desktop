<div x-data="{ confirmId: null }">

    {{-- Custom Delete Confirm Modal --}}
    <template x-if="confirmId !== null">
        <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 400px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                <div style="padding: 1.75rem 2rem;">
                    <div style="width: 48px; height: 48px; background: var(--danger-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                        <i class="ph ph-trash" style="font-size: 1.5rem; color: var(--danger);"></i>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Delete Hearing Record?</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">This action cannot be undone. The hearing record will be permanently removed.</p>
                </div>
                <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button @click="confirmId = null" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                        Cancel
                    </button>
                    <button @click="$wire.deleteHearing(confirmId); confirmId = null" type="button"
                        style="height: 40px; padding: 0 1.25rem; background: var(--danger); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ph ph-trash"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </template>
    {{-- Hearing Form Modal Moved to Index --}}

    {{-- Filters Section --}}
    <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-hover); display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
        <div style="position: relative; flex: 1; min-width: 300px; max-width: 400px;">
            <i class="ph ph-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1rem;"></i>
            <input wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search cases, names..."
                class="search-input"
                style="padding-left: 2.75rem; width: 100%;">
        </div>

        <div style="display: flex; gap: 1rem;">
            <select wire:model.live="status" class="search-input" style="width: auto; min-width: 160px; height: 42px;">
                <option value="">All Statuses</option>
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="failed">Failed</option>
            </select>

            <select wire:model.live="type" class="search-input" style="width: auto; min-width: 160px; height: 42px;">
                <option value="">All Types</option>
                <option value="mediation">Mediation</option>
                <option value="conciliation">Conciliation</option>
                <option value="arbitration">Arbitration</option>
            </select>
        </div>
    </div>

    {{-- Table Container --}}
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--bg-hover); border-bottom: 2px solid var(--border-color);">
                    <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Case Details</th>
                    <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Parties</th>
                    <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Category</th>
                    <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Schedule</th>
                    <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Status</th>
                    <th style="padding: 1.25rem 2rem; text-align: right; font-size: 0.6875rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hearings as $hearing)
                    @php
                        $isPast = $hearing->scheduled_at->isPast() && $hearing->status === 'scheduled';
                        $statusBadge = match($hearing->status) {
                            'completed' => 'badge-success',
                            'cancelled', 'failed' => 'badge-danger',
                            default => 'badge-info',
                        };
                        $typeBadge = match($hearing->hearing_type) {
                            'conciliation' => 'badge-info',
                            'arbitration' => 'badge-warning',
                            default => 'badge-success',
                        };
                    @endphp
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='var(--bg-card)'">
                        <td style="padding: 1.5rem 2rem;">
                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                <span style="font-size: 0.9375rem; font-weight: 700; color: var(--text-primary);">{{ $hearing->luponCase->nature_of_case }}</span>
                                <a href="{{ route('cases.show', $hearing->luponCase) }}" style="font-size: 0.75rem; font-weight: 600; color: var(--accent-blue); text-decoration: none;">
                                    Case #{{ $hearing->luponCase->case_number }}
                                </a>
                            </div>
                        </td>
                        <td style="padding: 1.5rem 2rem;">
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 0.625rem; font-weight: 800; background: var(--accent-light); color: var(--accent-blue); padding: 2px 4px; border-radius: 4px;">C</span>
                                    <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-secondary);">{{ Str::limit($hearing->luponCase->complainant, 20) }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 0.625rem; font-weight: 800; background: var(--danger-light); color: var(--danger); padding: 2px 4px; border-radius: 4px;">R</span>
                                    <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-secondary);">{{ Str::limit($hearing->luponCase->respondent, 20) }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1.5rem 2rem;">
                            <span class="badge {{ $typeBadge }}" style="text-transform: capitalize;">
                                {{ $hearing->hearing_type }}
                            </span>
                        </td>
                        <td style="padding: 1.5rem 2rem;">
                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                <span style="font-size: 0.875rem; font-weight: 700; color: {{ $isPast ? 'var(--danger)' : 'var(--text-primary)' }};">
                                    {{ $hearing->scheduled_at->format('M d, Y') }}
                                </span>
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">
                                    {{ $hearing->scheduled_at->format('h:i A') }}
                                    @if($isPast) <span style="color: var(--danger); font-weight: 800; margin-left: 0.5rem;">• OVERDUE</span> @endif
                                </span>
                            </div>
                        </td>
                        <td style="padding: 1.5rem 2rem;">
                            <span class="badge {{ $statusBadge }}" style="text-transform: capitalize;">
                                {{ $hearing->status }}
                            </span>
                        </td>
                        <td style="padding: 1.5rem 2rem; text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('hearings.show', $hearing) }}" class="btn-icon" title="View Detail" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--bg-hover); border: 1px solid var(--border-color); text-decoration: none;">
                                    <i class="ph ph-eye"></i>
                                </a>
                                <button wire:click="$dispatch('editHearing', { hearingId: {{ $hearing->id }} })" class="btn-icon" title="Edit" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--bg-hover); border: 1px solid var(--border-color);">
                                    <i class="ph ph-pencil"></i>
                                </button>
                                <button @click="confirmId = {{ $hearing->id }}" type="button" class="btn-icon" title="Delete" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--danger-light); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.1);">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 6rem; text-align: center; color: var(--text-muted);">
                            <i class="ph ph-calendar-blank" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.25rem;">No matching hearings found</h3>
                            <p style="font-size: 0.875rem;">Adjust your filters or schedule a new session.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($hearings->hasPages())
        <div style="padding: 1.5rem 2rem; background: var(--bg-hover); border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-secondary);">
                Current View: {{ $hearings->firstItem() ?? 0 }}-{{ $hearings->lastItem() ?? 0 }} of {{ $hearings->total() }} records
            </div>
            <div class="pagination-links">
                {{ $hearings->links() }}
            </div>
        </div>
    @endif
</div>
