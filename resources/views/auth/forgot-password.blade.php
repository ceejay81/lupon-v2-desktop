<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Lupon | Barangay Bula Justice System</title>
    <link rel="stylesheet" href="{{ asset('vendor/fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login-banig.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/phosphor-icons/regular/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-left-content">
                <div class="login-illustration-frame">
                    <div class="login-illustration-image" style="background-image: url('{{ asset('images/luponloginlogo.png') }}');"></div>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-form-wrapper">
                <div class="login-logo">
                    <img src="{{ asset('images/bulalogo.png') }}" alt="Bula Logo" style="width: 56px; height: 56px; border-radius: 50%; object-fit: contain;">
                </div>

                <div class="login-form-header">
                    <h1>Reset Password</h1>
                    <p>Enter your registered email to set a new password.</p>
                </div>

                @if(session('status'))
                    <div class="login-error" style="background-color: var(--success-light); color: var(--success); border-color: rgba(34,197,94,0.2);">
                        <i class="ph ph-check-circle"></i> {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="login-error">
                        <i class="ph ph-warning-circle"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.reset') }}" id="reset-form">
                    @csrf

                    {{-- Email --}}
                    <div class="form-group" style="position: relative;">
                        <input type="email" id="email" name="email" class="form-input"
                               value="{{ old('email') }}" required autocomplete="email"
                               placeholder=" " oninput="checkEmail(this.value)">
                        <label for="email" class="form-label">Email Address</label>
                        <span id="email-check" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.1rem; display: none;"></span>
                    </div>

                    {{-- New Password --}}
                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-input"
                               required placeholder=" " minlength="6">
                        <label for="password" class="form-label">New Password</label>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="form-group">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-input" required placeholder=" " minlength="6">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    </div>

                    <button type="submit" class="login-btn" id="submit-btn">
                        Reset Password
                    </button>
                </form>

                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="{{ route('login') }}" style="font-size: 0.875rem; color: var(--accent-blue); text-decoration: none; font-weight: 600;">
                        <i class="ph ph-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let checkTimeout;

        function checkEmail(value) {
            clearTimeout(checkTimeout);
            const icon = document.getElementById('email-check');
            icon.style.display = 'none';

            if (!value || !value.includes('@')) return;

            checkTimeout = setTimeout(() => {
                fetch('{{ route('password.check-email') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email: value })
                })
                .then(r => r.json())
                .then(data => {
                    icon.style.display = 'inline';
                    if (data.exists) {
                        icon.innerHTML = '<i class="ph ph-check-circle" style="color: #10b981;"></i>';
                    } else {
                        icon.innerHTML = '<i class="ph ph-x-circle" style="color: #ef4444;"></i>';
                    }
                });
            }, 400);
        }
    </script>
</body>
</html>
