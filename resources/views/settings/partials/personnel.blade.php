{{-- Personnel Management Tab --}}
<div class="settings-tab-panel active" id="tab-personnel" x-data="{ confirmDelete: false, deleteUrl: '', deleteName: '' }">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="ph ph-users-three"></i> Lupon Members</h2>
            <button class="btn-primary btn-sm" onclick="openMemberModal()">
                <i class="ph ph-plus"></i> Register Member
            </button>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="settings-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Appointed</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <td style="font-weight: 600; color: var(--text-primary);">{{ $member->name }}</td>
                        <td>{{ $member->position }}</td>
                        <td>
                            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: capitalize;">
                                {{ str_replace('_', ' ', $member->member_type) }}
                            </span>
                        </td>
                        <td>{{ $member->appointment_date ? $member->appointment_date->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $member->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $member->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                <button class="btn-sm" title="Edit Member"
                                    onclick="openMemberModal({
                                        id: {{ $member->id }},
                                        name: '{{ addslashes($member->name) }}',
                                        position: '{{ addslashes($member->position) }}',
                                        member_type: '{{ $member->member_type }}',
                                        appointment_date: '{{ $member->appointment_date ? $member->appointment_date->format('Y-m-d') : '' }}'
                                    })">
                                    <i class="ph ph-pencil"></i>
                                </button>
                                <form action="{{ route('settings.members.toggle', $member) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-sm" title="{{ $member->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="ph {{ $member->is_active ? 'ph-user-minus' : 'ph-user-plus' }}"></i>
                                    </button>
                                </form>
                                
                                <button type="button" class="btn-sm" style="color: var(--danger);" title="Delete"
                                        @click="confirmDelete = true; deleteUrl = '{{ route('settings.members.destroy', $member) }}'; deleteName = '{{ addslashes($member->name) }}'">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <i class="ph ph-users-three" style="font-size: 2.5rem; opacity: 0.3; display: block; margin-bottom: 8px;"></i>
                            No Lupon members registered yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Member Deletion Modal --}}
    <template x-if="confirmDelete">
        <div style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 9000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background: var(--bg-card); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); width: 100%; max-width: 420px; overflow: hidden; animation: modal-slide-up 0.2s ease-out;">
                <div style="padding: 1.75rem 2rem;">
                    <div style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                        <i class="ph ph-user-minus" style="font-size: 1.5rem; color: var(--danger);"></i>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">Remove Member?</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">Are you sure you want to remove <strong x-text="deleteName"></strong>? This action cannot be undone.</p>
                </div>
                <div style="padding: 1rem 2rem 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button @click="confirmDelete = false" type="button" class="btn-sm" style="height: 40px; padding: 0 1.25rem;">
                        Cancel
                    </button>
                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--danger); color: white; border: none; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph ph-trash"></i> Yes, Remove Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
