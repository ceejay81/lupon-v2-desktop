<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Lupon | Barangay Bula Justice System</title>
    <link rel="stylesheet" href="{{ asset('vendor/fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login-banig.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/phosphor-icons/regular/style.css') }}">
</head>

<body>
    <div class="login-container">
        <!-- Left Panel — Filipino Banig Pattern with Framed Illustration -->
        <div class="login-left">
            <div class="login-left-content">
                <!-- Framed illustration with warm tan textured border -->
                <div class="login-illustration-frame">
                    <img src="{{ asset('images/luponloginlogo.png') }}" alt="Lupon Illustration" class="login-illustration-image">
                </div>
            </div>
        </div>

        <!-- Right Panel — Login Form -->
        <div class="login-right">
            <div class="login-form-wrapper">
                <!-- Logo Section -->
                <div class="login-logo">
                    <img src="{{ asset('images/bulalogo.png') }}" alt="Bula Logo" class="login-logo-image">
                    <h2 class="login-logo-title">Lupon</h2>
                    <p class="login-logo-subtitle">Barangay Bula Justice System</p>
                </div>

                <div class="login-form-header">
                    <h1>Magandang araw!</h1>
                    <p>Sign in to access the Lupong Tagapamayapa system</p>
                </div>

                {{-- Status / Success Messages --}}
                @if(session('status'))
                    <div class="login-error" style="background-color: var(--success-light); color: var(--success); border-color: rgba(34,197,94,0.2);">
                        <i class="ph ph-check-circle"></i> {{ session('status') }}
                    </div>
                @endif

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="login-error">
                        <i class="ph ph-warning-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-group">
                        <input type="email" id="email" name="email" class="form-input"
                            placeholder="Email address" value="{{ old('email') }}" autofocus autocomplete="email">
                        <label for="email" class="form-label">Email address</label>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" class="form-input"
                                placeholder="Password" autocomplete="current-password">
                            <label for="password" class="form-label">Password</label>
                            <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                                <i class="ph ph-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('password.forgot') }}" class="forgot-link">Forgot password?</a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="login-btn">Sign In</button>
                </form>


                <!-- Footer -->
                <div class="login-footer">
                    <p>Authorized personnel only. <a href="#">Contact Barangay Admin</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('ph-eye');
                toggleIcon.classList.add('ph-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('ph-eye-slash');
                toggleIcon.classList.add('ph-eye');
            }
        }
    </script>
</body>

</html>
