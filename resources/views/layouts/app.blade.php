<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Malasakit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Preconnect to CDNs -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Loading Screen -->
    <style>
        /* Hide scrollbar during loading */
        body:has(#page-loader:not(.loaded)) {
            overflow: hidden !important;
        }

        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: opacity 0.6s ease;
        }
        #page-loader.dark {
            background: #151718;
        }
        #page-loader.loaded {
            opacity: 0;
            pointer-events: none;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e9ecef;
            border-top-color: #009fb1;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        #page-loader.dark .spinner {
            border-color: #2a2f35;
            border-top-color: #009fb1;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Dark-to-Light Fade Overlay */
        #dark-fade-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #151718;
            z-index: 100000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.8s ease;
        }
        #dark-fade-overlay.active {
            opacity: 1;
        }
    </style>
    <script>
        // Check if coming from dark mode logout (one-time flag)
        (function() {
            var isLogoutFromDark = sessionStorage.getItem('logout-from-dark') === 'true';
            var darkFadeOverlay = document.createElement('div');
            darkFadeOverlay.id = 'dark-fade-overlay';
            
            if (isLogoutFromDark) {
                // Coming from dark mode logout - show dark overlay that will fade out
                darkFadeOverlay.className = 'active';
                // Clear the flag immediately so it doesn't persist
                sessionStorage.removeItem('logout-from-dark');
                document.write('<div id="page-loader" class="dark"><div class="spinner"></div></div>');
            } else {
                // Normal page load - show light loader
                document.write('<div id="page-loader"><div class="spinner"></div></div>');
            }
            
            document.write(darkFadeOverlay.outerHTML);
            document.documentElement.style.overflow = 'hidden';
        })();
    </script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"
        media="print" onload="this.media='all'">
    <noscript>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </noscript>

    <style>
        :root {
            --bs-primary: #009fb1;
            --bs-primary-rgb: 0, 159, 177;
            --bs-link-color: #009fb1;
            --bs-link-hover-color: #008a9a;
        }
        .text-primary { color: #009fb1 !important; }
        .bg-primary { background-color: #009fb1 !important; }
        .btn-primary {
            background-color: #009fb1 !important;
            border-color: #009fb1 !important;
        }
        .btn-primary:hover {
            background-color: #008a9a !important;
            border-color: #007d8a !important;
        }
        .btn-outline-primary {
            color: #009fb1 !important;
            border-color: #009fb1 !important;
        }
        .btn-outline-primary:hover {
            background-color: #009fb1 !important;
            color: #fff !important;
        }
        .border-primary { border-color: #009fb1 !important; }
        a { color: #009fb1; text-decoration: none; }
        a:hover { color: #008a9a; }

        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden
        }

        /* Critical Layout CSS for CLS Prevention */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%
        }

        .admin-sidebar {
            width: 250px;
            background: #f8f9fa;
            color: #495057;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
            border-right: 1px solid #e9ecef
        }

        .admin-main {
            flex: 1;
            margin-left: 250px;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - 250px);
            overflow-x: hidden;
            contain: paint
        }

        @media (max-width:768px) {
            .admin-sidebar {
                transform: translateX(-100%)
            }

            .admin-main {
                margin-left: 0;
                width: 100%
            }
        }

        /* Remaining styles */
        .admin-sidebar .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, .1)
        }

        .admin-sidebar .sidebar-header h4 {
            color: #2c3e50;
            margin: 0;
            font-weight: 600
        }

        .admin-sidebar .sidebar-header p {
            color: #6c757d;
            margin: 0;
            font-size: .9rem
        }

        .admin-sidebar .nav-link {
            color: #495057;
            padding: 1rem 1.5rem;
            border: none;
            display: flex;
            align-items: center;
            transition: all .3s ease
        }

        .admin-sidebar .nav-link:hover {
            background: #e9ecef;
            color: #495057
        }

        .admin-sidebar .nav-link.active {
            background: #009fb1;
            color: #fff
        }

        .admin-sidebar .nav-link i {
            margin-right: .75rem;
            width: 20px
        }

        .admin-header {
            background: #fff;
            color: #212529;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            box-shadow: none;
            width: 100%
        }

        .admin-header .header-left {
            display: flex;
            align-items: center
        }

        .admin-header .header-left .logo {
            display: flex;
            align-items: center;
            margin-right: 2rem
        }

        .admin-header .header-left .logo i {
            font-size: 1.5rem;
            margin-right: .5rem
        }

        .admin-header .header-left h3 {
            margin: 0;
            font-weight: 600
        }

        .admin-header .header-nav {
            display: flex;
            align-items: center
        }

        .admin-header .header-nav .nav-link {
            color: #495057;
            margin: 0 1rem;
            padding: .5rem 1rem;
            border-radius: 4px;
            transition: background .3s ease
        }

        .admin-header .header-nav .nav-link:hover {
            background: #f8f9fa;
            color: #212529
        }

        .admin-header .header-right {
            display: flex;
            align-items: center
        }

        .admin-header .header-right .user-info {
            display: flex;
            align-items: center;
            margin-left: 1rem
        }

        .admin-header .header-right .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #009fb1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: .75rem;
            font-weight: 600;
            color: #fff
        }

        .admin-header .header-right .user-details h6 {
            margin: 0;
            font-weight: 600
        }

        .admin-header .header-right .user-details small {
            opacity: .8
        }

        .admin-content {
            padding: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 100%
        }

        .public-layout header .navbar {
            background: #009fb1 !important;
            min-height: 80px
        }

        .public-layout .navbar {
            background: #009fb1;
            min-height: 80px
        }

        .navbar-brand img {
            height: 65px;
            width: auto;
            margin-right: 1rem;
            object-fit: contain
        }

        .public-layout .navbar-brand img {
            height: 65px;
            margin-right: 1rem
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: .75rem
        }

        .navbar-brand span {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2
        }

        .public-layout .navbar-brand span {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff
        }

        .public-layout .navbar-nav .nav-link {
            color: #fff;
            font-weight: 500;
            margin: 0 1rem;
            padding: .5rem 1rem;
            border-radius: 4px;
            transition: background .3s ease
        }

        .public-layout .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, .1);
            color: #fff
        }

        .public-layout .navbar-nav .nav-link.active {
            background: rgba(255, 255, 255, .2);
            color: #fff
        }

        @media (max-width:768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform .3s ease
            }

            .admin-sidebar.show {
                transform: translateX(0)
            }

            .admin-main {
                margin-left: 0;
                width: 100%
            }

            .admin-header {
                padding: 1rem
            }

            .admin-content {
                padding: 1rem
            }
        }

        footer {
            background: #e9ecef;
            color: #6c757d
        }

        /* White scrollbar for public pages */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c0c0c0;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }

        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #c0c0c0 #f0f0f0;
        }

        /* Global Button Overrides */
        .btn-primary {
            background-color: #009fb1 !important;
            border-color: #009fb1 !important;
            color: #fff !important;
        }

        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #008a9a !important;
            border-color: #008a9a !important;
            color: #fff !important;
        }

        .btn-outline-primary {
            color: #009fb1 !important;
            border-color: #009fb1 !important;
        }

        .btn-outline-primary:hover {
            background-color: #009fb1 !important;
            color: #fff !important;
        }

        /* Unified Alert/Message Colors */
        .alert-success, .badge.bg-success, .btn-success {
            background-color: #00D100 !important;
            border-color: #00D100 !important;
            color: #fff !important;
        }
        .text-success { color: #00D100 !important; }
        
        .alert-warning, .badge.bg-warning, .btn-warning {
            background-color: #D1D100 !important;
            border-color: #D1D100 !important;
            color: #fff !important;
        }
        .text-warning { color: #D1D100 !important; }

        .alert-danger, .badge.bg-danger, .btn-danger {
            background-color: #D10000 !important;
            border-color: #D10000 !important;
            color: #fff !important;
        }
        .text-danger { color: #D10000 !important; }

        .alert-info, .badge.bg-info, .btn-info {
            background-color: #009fb1 !important;
            border-color: #009fb1 !important;
            color: #fff !important;
        }
        .text-info { color: #009fb1 !important; }

        /* Global Border Utility Overrides */
        .border-primary { border-color: #009fb1 !important; }
        .border-success { border-color: #00D100 !important; }
        .border-warning { border-color: #D1D100 !important; }
        .border-danger { border-color: #D10000 !important; }
        .border-info { border-color: #009fb1 !important; }
    </style>

    @stack('styles')
</head>

<body class="{{ isset($adminLayout) && $adminLayout ? 'admin-layout' : 'public-layout' }}">
    @if(isset($adminLayout) && $adminLayout)
        <!-- Admin Layout -->
        <div class="admin-layout">
            <!-- Sidebar -->
            <div class="admin-sidebar">
                <div class="sidebar-header">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('images/malasakit-logo-blue.webp') }}" alt="MALASAKIT Logo" loading="lazy"
                            style="width: 40px; height: 40px; margin-right: 0.5rem;">
                        <div>
                            <h4>MALASAKIT</h4>
                        </div>
                    </div>
                </div>
                <nav class="nav flex-column">
                    @if(\App\Helpers\AuthHelper::user()->isSuperAdmin())
                        <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                            <i class="fas fa-th-large"></i> Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('superadmin.users') ? 'active' : '' }}" href="{{ route('superadmin.users') }}">
                            <i class="fas fa-users"></i> Patient Management
                        </a>
                        <a class="nav-link {{ request()->routeIs('superadmin.system-logs') ? 'active' : '' }}" href="{{ route('superadmin.system-logs') }}">
                            <i class="fas fa-calendar-check"></i> Appointments
                        </a>
                        <a class="nav-link {{ request()->routeIs('superadmin.analytics') ? 'active' : '' }}" href="{{ route('superadmin.analytics') }}">
                            <i class="fas fa-list"></i> Services & Reports
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-boxes"></i> Inventory
                        </a>
                    @else
                            <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" href="{{ route('patient.dashboard') }}">
                                <i class="fas fa-th-large"></i> Dashboard
                            </a>
                            <a class="nav-link {{ request()->routeIs('patient.appointments') ? 'active' : '' }}" href="{{ route('patient.appointments') }}">
                                <i class="fas fa-calendar"></i> My Appointments
                            </a>
                            <a class="nav-link {{ request()->routeIs('patient.book-appointment') ? 'active' : '' }}" href="{{ route('patient.book-appointment') }}">
                                <i class="fas fa-plus"></i> Book Appointment
                            </a>
                        @endif
                    </nav>
                </div>

                <!-- Main Content -->
                <div class="admin-main">
                    <!-- Header -->
                    <div class="admin-header">
                        <div class="header-left">
                            <div class="logo"><h3></h3></div>
                        </div>
                        <div class="header-right">
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUserApp" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-avatar" style="overflow: hidden;">
                                        @if(\App\Helpers\AuthHelper::user()->profile_picture)
                                            <img src="{{ asset('storage/' . \App\Helpers\AuthHelper::user()->profile_picture) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            {{ substr(\App\Helpers\AuthHelper::user()->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="fw-bold me-2">{{ \App\Helpers\AuthHelper::user()->name }}</div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUserApp">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="admin-content">
                        @yield('content')
                    </div>

                    <!-- Footer -->
                    <div class="text-center text-muted py-4 mt-auto border-top">
                        <div class="container small">
                            Barangay Health Clinic &copy; {{ date('Y') }}. All rights reserved.
                            <span class="mx-2">|</span>
                            <a href="{{ url('/privacy') }}" class="text-decoration-none text-muted">Privacy Policy</a>
                            <span class="mx-2">|</span>
                            <a href="{{ url('/terms') }}" class="text-decoration-none text-muted">Terms of Service</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout Form -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    @else
        <!-- Public Layout -->
        <header>
            <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                        <img src="{{ asset('images/malasakit-logo.png') }}" alt="Malasakit Logo" loading="lazy">
                        <span class="fw-bold text-white">MALASAKIT</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="mainNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active fw-bold' : '' }}" href="{{ url('/') }}">Home</a></li>
                            <li class="nav-item"><a class="nav-link {{ request()->is('policy') ? 'active fw-bold' : '' }}" href="{{ url('/policy') }}">Booking Policy</a></li>
                            <li class="nav-item"><a class="nav-link {{ request()->is('contact') ? 'active fw-bold' : '' }}" href="{{ url('/contact') }}">Contact Us!</a></li>

                            @if(!\App\Helpers\AuthHelper::check())
                                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                            @else
                                @if(\App\Helpers\AuthHelper::user()->isSuperAdmin())
                                    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
                                @elseif(\App\Helpers\AuthHelper::user()->isAdmin())
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                                @else
                                    <li class="nav-item"><a class="nav-link" href="{{ route('patient.dashboard') }}">Dashboard</a></li>
                                @endif
                                <li class="nav-item">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            @yield('content')
        </main>
    @endif

    @unless(isset($adminLayout) && $adminLayout)
        <footer class="text-center text-muted py-4 mt-auto">
            <div class="container small">
                Barangay Health Clinic &copy; {{ date('Y') }}. All rights reserved.
                <span class="mx-2">|</span>
                <a href="{{ url('/privacy') }}" class="text-decoration-none text-muted">Privacy Policy</a>
                <span class="mx-2">|</span>
                <a href="{{ url('/terms') }}" class="text-decoration-none text-muted">Terms of Service</a>
            </div>
        </footer>
    @endunless

    @php
        $flashType = null;
        $flashMessage = null;

        if (session('success')) {
            $flashType = 'success';
            $flashMessage = session('success');
        } elseif (session('status')) {
            $flashType = 'success';
            $flashMessage = session('status');
        } elseif (session('error')) {
            $flashType = 'danger';
            $flashMessage = session('error');
        } elseif (session('warning')) {
            $flashType = 'warning';
            $flashMessage = session('warning');
        } elseif (session('info')) {
            $flashType = 'info';
            $flashMessage = session('info');
        }

        $flashTitleMap = [
            'success' => 'Success',
            'danger' => 'Something went wrong',
            'warning' => 'Heads up',
            'info' => 'Notice',
        ];

        $flashIconMap = [
            'success' => 'fa-circle-check',
            'danger' => 'fa-triangle-exclamation',
            'warning' => 'fa-circle-exclamation',
            'info' => 'fa-circle-info',
        ];
    @endphp

    @if($flashType && $flashMessage)
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-{{ $flashType }} text-white">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="feedbackModalLabel">
                            <i class="fas {{ $flashIconMap[$flashType] ?? 'fa-circle-info' }}"></i>
                            {{ $flashTitleMap[$flashType] ?? 'Notice' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <p class="mb-0">{{ $flashMessage }}</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-{{ $flashType === 'danger' ? 'danger' : 'primary' }}" data-bs-dismiss="modal">Got it</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="loginModalLabel">Login to Continue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('login') }}" >
                        @csrf
                        <div class="mb-3">
                            <label for="login-email" class="form-label">Email address</label>
                            <input 
                                id="login-email"
                                type="email" 
                                name="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="form-label">Password</label>
                            <div class="input-group">
                                <input 
                                    id="login-password"
                                    type="password" 
                                    name="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    required
                                >
                                <button class="btn btn-outline-secondary" type="button" id="login-show-password-btn">
                                    <i id="login-show-password-icon" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-info w-100 text-white" id="loginSubmitBtn">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <small>Don't have an account? 
                            <a href="{{ route('register') }}">Register</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="registerModalLabel">Register an Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @php
                        $selectedBarangayModal = old('barangay');
                        $purokOptionsModal = match ($selectedBarangayModal) {
                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                            default => [],
                        };
                    @endphp
                    <form method="POST" action="{{ route('register') }}" class="registration-form">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                @if (str_contains($message, 'should not contain numbers'))
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @elseif ($message)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Enter your phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select name="barangay" class="form-control @error('barangay') is-invalid @enderror" data-role="barangay" required>
                                <option value="" disabled selected>Select Barangay</option>
                                <option value="Barangay 11" {{ $selectedBarangayModal === 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                                <option value="Barangay 12" {{ $selectedBarangayModal === 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                                <option value="Other" {{ $selectedBarangayModal === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('barangay')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 {{ $selectedBarangayModal === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                            <label class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                            <input type="text" name="barangay_other" class="form-control @error('barangay_other') is-invalid @enderror" value="{{ old('barangay_other') }}" data-role="barangay-other">
                            @error('barangay_other')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 {{ in_array($selectedBarangayModal, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                            <label class="form-label">Purok <span class="text-danger">*</span></label>
                            <select name="purok" class="form-control @error('purok') is-invalid @enderror" data-role="purok" data-selected="{{ old('purok') }}">
                                <option value="" disabled selected>Select Purok</option>
                                @foreach ($purokOptionsModal as $purok)
                                    <option value="{{ $purok }}" {{ old('purok') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                                @endforeach
                            </select>
                            @error('purok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                            <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" data-role="birth-date" max="{{ now()->toDateString() }}" required>
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required data-role="register-password-input">
                                <button class="btn btn-outline-secondary @error('password') d-none @enderror" type="button" data-role="register-password-toggle-btn">
                                    <i class="fa-solid fa-eye" data-role="register-password-toggle-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                @if (str_contains($message, 'lowercase letter') || str_contains($message, 'uppercase letter') || str_contains($message, 'special character'))
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @elseif ($message)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-info w-100 text-white">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        function requireLogin(){@if(\App\Helpers\AuthHelper::check()) @if(\App\Helpers\AuthHelper::user()->isPatient()) window.location.href="{{ route('patient.book-appointment') }}"; @else window.location.href="{{ \App\Helpers\AuthHelper::user()->isSuperAdmin() ? route('superadmin.dashboard') : route('admin.dashboard') }}"; @endif @else window.location.href="{{ route('login') }}"; @endif}
        document.addEventListener('DOMContentLoaded',function(){const feedbackModalEl=document.getElementById('feedbackModal');if(feedbackModalEl){const feedbackModal=new bootstrap.Modal(feedbackModalEl);feedbackModal.show();}@if($errors->has('email') || $errors->has('password')) const loginModalEl=document.getElementById('loginModal');if(loginModalEl){const loginModal=new bootstrap.Modal(loginModalEl);loginModal.show();}@endif const loginPasswordInput=document.getElementById('login-password');const loginShowPasswordBtn=document.getElementById('login-show-password-btn');const loginShowPasswordIcon=document.getElementById('login-show-password-icon');if(loginShowPasswordBtn&&loginPasswordInput){loginShowPasswordBtn.addEventListener('click',function(){const showing=loginPasswordInput.type==='text';const show=!showing;loginPasswordInput.type=show?'text':'password';if(loginShowPasswordIcon){if(show){loginShowPasswordIcon.classList.remove('fa-eye');loginShowPasswordIcon.classList.add('fa-eye-slash');}else{loginShowPasswordIcon.classList.remove('fa-eye-slash');loginShowPasswordIcon.classList.add('fa-eye');}}});}const barangayPurokMap={'Barangay 11':['Purok 1','Purok 2','Purok 3','Purok 4','Purok 5'],'Barangay 12':['Purok 1','Purok 2','Purok 3']};const registrationForms=document.querySelectorAll('.registration-form');registrationForms.forEach((form)=>{const barangaySelect=form.querySelector('[data-role="barangay"]');const barangayOtherGroup=form.querySelector('[data-role="barangay-other-group"]');const barangayOtherInput=form.querySelector('[data-role="barangay-other"]');const purokGroup=form.querySelector('[data-role="purok-group"]');const purokSelect=form.querySelector('[data-role="purok"]');const updatePurokOptions=(barangay)=>{if(!purokSelect)return;const previouslySelected=purokSelect.getAttribute('data-selected');purokSelect.innerHTML='<option value="" disabled selected>Select Purok</option>';if(!barangayPurokMap[barangay]){purokSelect.removeAttribute('required');purokSelect.setAttribute('data-selected','');return;}barangayPurokMap[barangay].forEach((purok)=>{const option=document.createElement('option');option.value=purok;option.textContent=purok;if(previouslySelected===purok){option.selected=true;}purokSelect.appendChild(option);});purokSelect.setAttribute('required','required');};const handleBarangayChange=()=>{const selectedBarangay=barangaySelect?barangaySelect.value:'';if(barangayOtherGroup&&barangayOtherInput){if(selectedBarangay==='Other'){barangayOtherGroup.classList.remove('d-none');barangayOtherInput.setAttribute('required','required');}else{barangayOtherGroup.classList.add('d-none');barangayOtherInput.removeAttribute('required');}}if(purokGroup&&purokSelect){if(barangayPurokMap[selectedBarangay]){purokGroup.classList.remove('d-none');updatePurokOptions(selectedBarangay);}else{purokGroup.classList.add('d-none');purokSelect.removeAttribute('required');purokSelect.value='';purokSelect.setAttribute('data-selected','');}}};if(barangaySelect){barangaySelect.addEventListener('change',()=>{if(purokSelect){purokSelect.setAttribute('data-selected','');}handleBarangayChange();});handleBarangayChange();}});});
                }
                const clearClientErrors = () => {
                    const clientErrors = form.querySelectorAll('.invalid-feedback.js-register-error');
                    clientErrors.forEach((el) => el.remove());
                    if (nameInput) {
                        nameInput.classList.remove('is-invalid');
                    }
                    if (phoneInput) {
                        phoneInput.classList.remove('is-invalid');
                    }
                    if (passwordInput) {
                        passwordInput.classList.remove('is-invalid');
                    }
                    if (passwordConfirmInput) {
                        passwordConfirmInput.classList.remove('is-invalid');
                    }
                };

                const appendError = (input, message) => {
                    if (!input) {
                        return;
                    }
                    input.classList.add('is-invalid');
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback js-register-error';
                    div.textContent = message;
                    input.insertAdjacentElement('afterend', div);
                };

                const validateNameAndPhone = () => {
                    clearClientErrors();
                    let valid = true;
                    let passwordHasError = false;

                    if (nameInput) {
                        const value = nameInput.value.trim();
                        const nameRegex = /^[a-zA-Z\s.\-']+$/;
                        if (!value) {
                            appendError(nameInput, 'Full name is required.');
                            valid = false;
                        } else if (!nameRegex.test(value)) {
                            appendError(nameInput, 'The name field should not contain numbers. Only letters, spaces, periods, hyphens, and apostrophes are allowed.');
                            valid = false;
                        }
                    }

                    if (phoneInput) {
                        const value = phoneInput.value.trim();
                        if (value && !/^\d{11}$/.test(value)) {
                            appendError(phoneInput, 'Phone number must be exactly 11 digits (e.g. 09123456789).');
                            valid = false;
                        }
                    }

                    if (passwordInput) {
                        const value = passwordInput.value;
                        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{}|,.<>\/?]).{8,}$/;
                        if (!value) {
                            appendError(passwordInput, 'Password is required.');
                            valid = false;
                            passwordHasError = true;
                        } else if (!passwordRegex.test(value)) {
                            appendError(passwordInput, 'Password must be at least 8 characters and include one uppercase letter, one lowercase letter, one number, and one special character.');
                            valid = false;
                            passwordHasError = true;
                        }
                    }

                    if (passwordInput && passwordConfirmInput) {
                        const value = passwordInput.value;
                        const confirmValue = passwordConfirmInput.value;
                        if (confirmValue && value !== confirmValue) {
                            appendError(passwordConfirmInput, 'Password and confirm password must match.');
                            valid = false;
                        }
                    }

                    // Show eye icon only when password currently has no client-side error
                    if (registerPasswordToggleBtn) {
                        if (passwordHasError) {
                            registerPasswordToggleBtn.classList.add('d-none');
                        } else {
                            registerPasswordToggleBtn.classList.remove('d-none');
                        }
                    }

                    return valid;
                };

                if (form) {
                    // Live validation: show errors as the user types or leaves the field
                    if (nameInput) {
                        nameInput.addEventListener('input', validateNameAndPhone);
                        nameInput.addEventListener('blur', validateNameAndPhone);
                    }
                    if (phoneInput) {
                        phoneInput.addEventListener('input', validateNameAndPhone);
                        phoneInput.addEventListener('blur', validateNameAndPhone);
                    }
                    if (passwordInput) {
                        passwordInput.addEventListener('input', validateNameAndPhone);
                        passwordInput.addEventListener('blur', validateNameAndPhone);
                    }
                    if (passwordConfirmInput) {
                        passwordConfirmInput.addEventListener('input', validateNameAndPhone);
                        passwordConfirmInput.addEventListener('blur', validateNameAndPhone);
                    }

                    if (registerPasswordToggleBtn && passwordInput) {
                        registerPasswordToggleBtn.addEventListener('click', function () {
                            const showing = passwordInput.type === 'text';
                            const show = !showing;

                            // Toggle both password and confirm password inputs
                            passwordInput.type = show ? 'text' : 'password';
                            if (passwordConfirmInput) {
                                passwordConfirmInput.type = show ? 'text' : 'password';
                            }

                            if (registerPasswordToggleIcon) {
                                if (show) {
                                    registerPasswordToggleIcon.classList.remove('fa-eye');
                                    registerPasswordToggleIcon.classList.add('fa-eye-slash');
                                } else {
                                    registerPasswordToggleIcon.classList.remove('fa-eye-slash');
                                    registerPasswordToggleIcon.classList.add('fa-eye');
                                }
                            }
                        });
                    }

                    // Final check on submit
                    form.addEventListener('submit', function (e) {
                        if (!validateNameAndPhone()) {
                            e.preventDefault();
                        }
                    });
                }

                // No additional birth date handling needed; age is calculated server-side.
            });
        });
    </script>
    <script>
        // Hide loading screen when page is ready with smooth dark-to-light transition
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            const darkFade = document.getElementById('dark-fade-overlay');
            // Check if this was a logout from dark mode (flag is already cleared but overlay is active)
            const wasDark = darkFade && darkFade.classList.contains('active');
            
            if (loader) {
                // Add a slight delay to ensure smooth transition
                setTimeout(() => {
                    loader.classList.add('loaded');
                    
                    // If coming from dark mode logout, fade the dark overlay after loader
                    if (wasDark && darkFade) {
                        setTimeout(() => {
                            darkFade.classList.remove('active');
                            // Restore overflow after all transitions
                            setTimeout(() => {
                                document.documentElement.style.overflow = '';
                            }, 800);
                        }, 300); // Wait for loader to fade
                    } else {
                        // Restore overflow immediately for light mode
                        document.documentElement.style.overflow = '';
                    }
                    
                    setTimeout(() => {
                        loader.remove();
                        if (darkFade && !wasDark) darkFade.remove();
                    }, 600);
                }, 100);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
