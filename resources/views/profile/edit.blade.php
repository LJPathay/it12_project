@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('sidebar-menu')
    @if(\App\Helpers\AuthHelper::user()->isSuperAdmin())
        <a class="nav-link" href="{{ route('superadmin.dashboard') }}" data-tooltip="Dashboard">
            <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
        </a>
        <a class="nav-link" href="{{ route('superadmin.users') }}" data-tooltip="User Management">
            <i class="fas fa-user"></i> <span class="sidebar-content">User Management</span>
        </a>
        <a class="nav-link" href="{{ route('superadmin.system-logs') }}" data-tooltip="System Logs">
            <i class="fas fa-list"></i> <span class="sidebar-content">System Logs</span>
        </a>
        <a class="nav-link" href="{{ route('superadmin.analytics') }}" data-tooltip="Analytics">
            <i class="fas fa-chart-bar"></i> <span class="sidebar-content">Analytics</span>
        </a>
        <a class="nav-link" href="{{ route('superadmin.backup') }}" data-tooltip="Backup">
            <i class="fas fa-download"></i> <span class="sidebar-content">Backup</span>
        </a>
    @elseif(\App\Helpers\AuthHelper::user()->isAdmin())
        <a class="nav-link" href="{{ route('admin.dashboard') }}" data-tooltip="Dashboard">
            <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
        </a>
        <a class="nav-link" href="{{ route('admin.patients') }}" data-tooltip="Patient Management">
            <i class="fas fa-user"></i> <span class="sidebar-content">Patient Management</span>
        </a>
        <a class="nav-link" href="{{ route('admin.appointments') }}" data-tooltip="Appointments">
            <i class="fas fa-calendar-check"></i> <span class="sidebar-content">Appointments</span>
        </a>
        <a class="nav-link" href="{{ route('admin.reports') }}" data-tooltip="Services & Reports">
            <i class="fas fa-chart-bar"></i> <span class="sidebar-content">Services & Reports</span>
        </a>
        <a class="nav-link" href="{{ route('admin.inventory') }}" data-tooltip="Inventory">
            <i class="fas fa-box"></i> <span class="sidebar-content">Inventory</span>
        </a>
    @else
        <a class="nav-link" href="{{ route('patient.dashboard') }}" data-tooltip="Dashboard">
            <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
        </a>
        <a class="nav-link" href="{{ route('patient.appointments') }}" data-tooltip="My Appointments">
            <i class="fas fa-calendar"></i> <span class="sidebar-content">My Appointments</span>
        </a>
        <a class="nav-link" href="{{ route('patient.book-appointment') }}" data-tooltip="Book Appointment">
            <i class="fas fa-plus"></i> <span class="sidebar-content">Book Appointment</span>
        </a>
    @endif
@endsection

@section('user-initials')
    @if(\App\Helpers\AuthHelper::user()->isSuperAdmin())
        SA
    @else
        {{ substr(\App\Helpers\AuthHelper::user()->name, 0, 2) }}
    @endif
@endsection

@section('user-name')
    @if(\App\Helpers\AuthHelper::user()->isSuperAdmin())
        Super Admin
    @else
        {{ \App\Helpers\AuthHelper::user()->name }}
    @endif
@endsection

@section('user-role')
    @if(\App\Helpers\AuthHelper::user()->isSuperAdmin())
        Administrator
    @elseif(\App\Helpers\AuthHelper::user()->isAdmin())
        {{ ucfirst(\App\Helpers\AuthHelper::user()->role) }}
    @else
        Patient
    @endif
@endsection

@push('styles')
    <style>
        /* Hide sidebar on profile page */
        .sidebar {
            display: none !important;
        }

        /* Adjust main content to full width */
        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }


        /* Dark mode support for profile page */
        body.bg-dark .card {
            background-color: #1a1a1a !important;
            border-color: #2d2d2d !important;
            border-radius: 16px !important;
            overflow: hidden;
        }

        /* Light mode card rounded corners */
        .card {
            border-radius: 16px !important;
            overflow: hidden;
        }


        body.bg-dark .card-body {
            background-color: #1a1a1a !important;
            color: #e6e6e6 !important;
        }

        body.bg-dark h4,
        body.bg-dark h5,
        body.bg-dark p {
            color: #e6e6e6 !important;
        }

        body.bg-dark .text-muted {
            color: #999 !important;
        }

        body.bg-dark .form-label {
            color: #e6e6e6 !important;
        }

        body.bg-dark .form-control {
            background-color: #151718 !important;
            border-color: #2d2d2d !important;
            color: #e6e6e6 !important;
        }

        body.bg-dark .form-control:focus {
            background-color: #151718 !important;
            border-color: #17a2b8 !important;
            color: #e6e6e6 !important;
            box-shadow: 0 0 0 0.25rem rgba(23, 162, 184, 0.25) !important;
        }

        body.bg-dark .form-control::placeholder {
            color: #666 !important;
        }

        body.bg-dark .form-text {
            color: #999 !important;
        }

        body.bg-dark hr {
            border-color: #404040 !important;
            opacity: 0.3 !important;
        }

        body.bg-dark .btn-outline-secondary {
            color: #e6e6e6 !important;
            border-color: #6c757d !important;
        }

        body.bg-dark .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            color: white !important;
        }

        body.bg-dark .invalid-feedback {
            color: #ff6b6b !important;
        }

        body.bg-dark .alert-success {
            background-color: #1a4d2e !important;
            border-color: #2d7a4f !important;
            color: #90ee90 !important;
        }

        /* Profile picture styling in dark mode */
        body.bg-dark .rounded-circle {
            border-color: #2d2d2d !important;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card class="shadow-sm border-0" noPadding>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="profileImagePreview"
                                    src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) . '?v=' . time() : '' }}"
                                    alt="Profile Picture"
                                    class="rounded-circle object-fit-cover {{ $user->profile_picture ? '' : 'd-none' }}"
                                    style="width: 120px; height: 120px; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">

                                <div id="profileInitials"
                                    class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto {{ $user->profile_picture ? 'd-none' : '' }}"
                                    style="width: 120px; height: 120px; font-size: 3rem; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                    {{ substr($user->name, 0, 1) }}
                                </div>

                                <label for="profile_picture"
                                    class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                    style="cursor: pointer; width: 36px; height: 36px;">
                                    <i class="fas fa-camera text-primary"></i>
                                    <input type="file" name="profile_picture" id="profile_picture" class="d-none"
                                        accept="image/*">
                                </label>
                            </div>
                            <h4 class="mt-3 mb-1">{{ $user->name }}</h4>
                            <p class="text-muted">{{ ucfirst($user->role) }}</p>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Personal Information</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <x-input name="name" label="Full Name" required value="{{ old('name', $user->name) }}" />
                            </div>
                            <div class="col-md-6">
                                <x-input name="email" label="Email Address" type="email" required
                                    value="{{ old('email', $user->email) }}" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-input name="phone" label="Phone Number" type="tel" placeholder="Enter your phone number"
                                    value="{{ old('phone', $user->phone) }}" />
                            </div>
                            <div class="col-md-6">
                                <x-input name="birth_date" label="Date of Birth" type="date"
                                    value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-medium">Address</label>
                            <textarea name="address" id="address" rows="3"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="Enter your full address">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Change Password</h5>
                        <p class="text-muted small mb-3">Leave the password fields blank if you don't want to change your
                            password.</p>

                        <x-input name="current_password" label="Current Password" type="password"
                            helper="Required if you want to change your password." />

                        <x-input name="password" label="New Password" type="password"
                            helper="Must be at least 8 characters long." />

                        <x-input name="password_confirmation" label="Confirm New Password" type="password" />

                        <div class="d-grid gap-2">
                            <x-button type="submit" variant="primary" icon="fas fa-save">Save Changes</x-button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('profile_picture');
            const imagePreview = document.getElementById('profileImagePreview');
            const initials = document.getElementById('profileInitials');

            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];

                    if (file) {
                        // Validate file type
                        if (!file.type.startsWith('image/')) {
                            alert('Please select an image file');
                            fileInput.value = '';
                            return;
                        }

                        // Validate file size (max 2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Image size must be less than 2MB');
                            fileInput.value = '';
                            return;
                        }

                        // Preview the image
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            imagePreview.src = e.target.result;
                            imagePreview.classList.remove('d-none');
                            initials.classList.add('d-none');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush