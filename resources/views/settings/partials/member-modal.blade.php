{{-- Member Modal --}}
<div class="settings-modal-overlay" id="memberModal" style="display: none;">
    <div class="card settings-modal-card">
        <div class="card-header">
            <h2 class="card-title" id="modalTitle">Add New Member</h2>
            <button class="btn-icon" onclick="closeMemberModal()"><i class="ph ph-x"></i></button>
        </div>
        <div class="card-body">
            <form id="memberForm" method="POST" action="{{ route('settings.members.store') }}">
                @csrf
                <input type="hidden" name="_method" id="memberFormMethod" value="POST">

                <div class="settings-field" style="margin-bottom: 16px;">
                    <label>Full Name</label>
                    <input type="text" name="name" id="memberName" required class="settings-input" placeholder="e.g. Juan De La Cruz">
                </div>

                <div class="settings-field" style="margin-bottom: 16px;">
                    <label>Position / Title</label>
                    <input type="text" name="position" id="memberPosition" required class="settings-input" placeholder="e.g. Lupon Secretary">
                </div>

                <div class="settings-field" style="margin-bottom: 16px;">
                    <label>Member Type</label>
                    <select name="member_type" id="memberType" required class="settings-input">
                        <option value="regular">Regular Member</option>
                        <option value="punong_barangay">Punong Barangay</option>
                        <option value="lupon_secretary">Lupon Secretary</option>
                    </select>
                </div>

                <div class="settings-field" style="margin-bottom: 24px;">
                    <label>Appointment Date (Optional)</label>
                    <input type="date" name="appointment_date" id="memberAppointment" class="settings-input">
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-sm" onclick="closeMemberModal()">Cancel</button>
                    <button type="submit" class="btn-primary" id="memberSubmitBtn">Register Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openMemberModal(member = null) {
        const modal = document.getElementById('memberModal');
        const form = document.getElementById('memberForm');
        const title = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('memberSubmitBtn');
        const methodField = document.getElementById('memberFormMethod');

        if (member) {
            title.textContent = 'Edit Member';
            submitBtn.textContent = 'Save Changes';
            form.action = '{{ route("settings.members.update", ":member") }}'.replace(':member', member.id);
            methodField.value = 'PUT';
            document.getElementById('memberName').value = member.name;
            document.getElementById('memberPosition').value = member.position;
            document.getElementById('memberType').value = member.member_type;
            document.getElementById('memberAppointment').value = member.appointment_date || '';
        } else {
            title.textContent = 'Add New Member';
            submitBtn.textContent = 'Register Member';
            form.action = '{{ route("settings.members.store") }}';
            methodField.value = 'POST';
            document.getElementById('memberName').value = '';
            document.getElementById('memberPosition').value = '';
            document.getElementById('memberType').value = 'regular';
            document.getElementById('memberAppointment').value = '';
        }

        modal.style.display = 'flex';
    }

    function closeMemberModal() {
        document.getElementById('memberModal').style.display = 'none';
    }

    // Close modal on backdrop click
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('memberModal').addEventListener('click', function(e) {
            if (e.target === this) closeMemberModal();
        });
    });
</script>
@endpush
