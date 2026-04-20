{{-- Security Tab --}}
<div class="settings-tab-panel" id="tab-security" style="display:none;">
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
        {{-- Update Name --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="ph ph-user"></i> Admin Name</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.security') }}" method="POST">
                    @csrf
                    <div class="settings-field">
                        <label>Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="settings-input" placeholder="Administrator Name">
                        @error('name') <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </div>
                    <div style="margin-top: 1.25rem;">
                        <button type="submit" class="btn-primary">Update Name</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Update Email --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="ph ph-envelope"></i> Email Address</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.security') }}" method="POST">
                    @csrf
                    <div class="settings-field">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="settings-input">
                        @error('email') <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </div>
                    <div style="margin-top: 1.25rem;">
                        <button type="submit" class="btn-primary">Update Email</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="ph ph-lock-key"></i> Change Password</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.security') }}" method="POST">
                    @csrf
                    <div class="settings-field">
                        <label>New Password</label>
                        <input type="password" name="password" class="settings-input" placeholder="Minimum 6 characters">
                        @error('password') <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p> @enderror
                    </div>
                    <div class="settings-field" style="margin-top: 0.75rem;">
                        <label>Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="settings-input">
                    </div>
                    <div style="margin-top: 1.25rem;">
                        <button type="submit" class="btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
