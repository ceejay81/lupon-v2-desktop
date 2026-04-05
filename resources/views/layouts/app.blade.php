<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lupon - ' . \App\Models\Setting::get('barangay_name', 'Barangay Bula') . ' Justice System')</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/phosphor-icons/regular/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Alpine.js is now managed natively by Livewire 3 --}}
</head>

<body x-data="{ sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true' }" x-effect="localStorage.setItem('sidebar-collapsed', sidebarCollapsed)">
    <!-- Mobile sidebar toggle (pure CSS) -->
    <input type="checkbox" id="mobile-sidebar-toggle" class="mobile-sidebar-checkbox">
    <label for="mobile-sidebar-toggle" class="mobile-hamburger" aria-label="Toggle navigation">
        <span></span>
        <span></span>
        <span></span>
    </label>
    <label for="mobile-sidebar-toggle" class="mobile-sidebar-backdrop"></label>

    <div class="app-container">
        <!-- Vertical Sidebar -->
        <aside class="sidebar" :class="{ 'collapsed': sidebarCollapsed }">
            <div class="sidebar-header">
                <div class="logo-wrapper">
                    <img src="{{ asset(\App\Models\Setting::get('barangay_logo_path') ?: 'images/bulalogo.png') }}" alt="Barangay Logo" class="sidebar-logo">
                    <div class="sidebar-title-group" x-show="!sidebarCollapsed" x-transition>
                        <div class="logo">Lupon</div>
                        <p class="logo-subtitle">{{ \App\Models\Setting::get('barangay_name', 'Barangay Bula') }}</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Toggle Tab -->
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="sidebar-toggle" title="Toggle Sidebar">
                <i class="ph-bold ph-caret-left sidebar-toggle-icon"></i>
            </button>

            <nav class="sidebar-nav">
                <div class="nav-group">
                    <span class="nav-label" x-show="!sidebarCollapsed">GENERAL</span>
                    <a href="{{ route('dashboard') }}"
                        class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ph ph-house"></i>
                        <span x-show="!sidebarCollapsed">Dashboard</span>
                    </a>
                    <a href="{{ route('cases.index') }}"
                        class="nav-item {{ request()->routeIs('cases.*') ? 'active' : '' }}">
                        <i class="ph ph-notebook"></i>
                        <span x-show="!sidebarCollapsed">Digital Blotter</span>
                    </a>
                    <a href="{{ route('hearings.index') }}"
                        class="nav-item {{ request()->routeIs('hearings.*') ? 'active' : '' }}">
                        <i class="ph ph-calendar-blank"></i>
                        <span x-show="!sidebarCollapsed">Hearings</span>
                    </a>
                    <a href="{{ route('citizens.index') }}"
                        class="nav-item {{ request()->routeIs('citizens.*') ? 'active' : '' }}">
                        <i class="ph ph-users-four"></i>
                        <span x-show="!sidebarCollapsed">Citizens</span>
                    </a>
                </div>

                <div class="nav-group">
                    <span class="nav-label" x-show="!sidebarCollapsed">COMPLIANCE</span>
                    <a href="{{ route('reports.index') }}"
                        class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="ph ph-file-text"></i>
                        <span x-show="!sidebarCollapsed">LTIA Reports</span>
                    </a>
                    <a href="{{ route('folderized-reports.index') }}"
                        class="nav-item {{ request()->routeIs('folderized-reports.*') ? 'active' : '' }}">
                        <i class="ph ph-files"></i>
                        <span x-show="!sidebarCollapsed">Digital Archives</span>
                    </a>
                </div>

                <div class="nav-group">
                    <span class="nav-label" x-show="!sidebarCollapsed">SYSTEM</span>
                    <a href="{{ route('settings.index') }}"
                        class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="ph ph-gear"></i>
                        <span x-show="!sidebarCollapsed">Admin Settings</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-item">
                            <i class="ph ph-sign-out"></i>
                            <span x-show="!sidebarCollapsed">Sign Out</span>
                        </a>
                    </form>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar" style="background: var(--accent-blue); color: white; font-weight: 700;">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'G' }}
                    </div>
                    <div class="user-details" x-show="!sidebarCollapsed">
                        <p class="user-name">{{ auth()->check() ? auth()->user()->name : 'Guest' }}</p>
                        <p class="user-role">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content" :class="{ 'collapsed': sidebarCollapsed }">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <h2 class="page-title">@yield('page-title', 'Dashboard')</h2>
                </div>
                <div class="top-bar-right">
                    <button id="theme-toggle" class="btn-icon" title="Toggle dark mode">
                        <i class="ph-duotone ph-moon" id="theme-icon"></i>
                    </button>

                    <div class="user-profile-circle" style="background: var(--gray-100); border: 1px solid var(--gray-200); color: var(--accent-blue); font-weight: 700;">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'G' }}
                    </div>
                </div>
            </header>

            @if(session('success') || session('error') || session('message') || session('warning') || session('status') || session('attendance_message') || session('pangkat_message') || $errors->any())
                <div class="alert-container">
                    @foreach([
                        'success'            => ['type' => 'success', 'icon' => 'ph-check-circle'],
                        'message'            => ['type' => 'success', 'icon' => 'ph-check-circle'],
                        'status'             => ['type' => 'success', 'icon' => 'ph-check-circle'],
                        'attendance_message' => ['type' => 'success', 'icon' => 'ph-check-circle'],
                        'pangkat_message'    => ['type' => 'success', 'icon' => 'ph-check-circle'],
                        'warning'            => ['type' => 'warning', 'icon' => 'ph-warning'],
                        'error'              => ['type' => 'error',   'icon' => 'ph-warning-circle'],
                    ] as $key => $config)
                        @if(session($key))
                            <div class="alert alert-{{ $config['type'] }}">
                                <i class="ph {{ $config['icon'] }}"></i>
                                <span>{{ session($key) }}</span>
                            </div>
                        @endif
                    @endforeach
                    @if($errors->any())
                        <div class="alert alert-error">
                            <i class="ph ph-warning-circle"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        (function () {
            const root = document.documentElement;
            const btn = document.getElementById('theme-toggle');
            const icon = document.getElementById('theme-icon');

            function applyTheme(dark) {
                root.setAttribute('data-theme', dark ? 'dark' : 'light');
                icon.className = dark ? 'ph ph-sun' : 'ph ph-moon';
            }

            const saved = localStorage.getItem('lupon-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(saved ? saved === 'dark' : prefersDark);

            btn.addEventListener('click', function () {
                const isDark = root.getAttribute('data-theme') === 'dark';
                localStorage.setItem('lupon-theme', isDark ? 'light' : 'dark');
                applyTheme(!isDark);
            });
        })();

        // Global Livewire toast handler
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toast', ({ type, message }) => {
                const icons = {
                    success: 'ph-check-circle',
                    error:   'ph-warning-circle',
                    warning: 'ph-warning',
                };

                let container = document.querySelector('.alert-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'alert-container';
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                toast.className = `alert alert-${type}`;
                toast.innerHTML = `<i class="ph ${icons[type] ?? 'ph-info'}"></i><span>${message}</span>`;
                container.appendChild(toast);

                setTimeout(() => toast.remove(), 3900);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>