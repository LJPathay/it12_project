@extends('layouts.app')

@push('styles')
<style>
    /* Darker borders for form inputs to match buttons */
    .form-control {
        border-color: #6c757d !important;
    }
    
    .form-control:focus {
        border-color: #495057 !important;
        box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25) !important;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d !important;
    }

    /* Password Strength Indicator */
    .strength-bar {
        flex: 1;
        height: 4px;
        background-color: #e0e0e0;
        border-radius: 2px;
        transition: background-color 0.3s ease;
    }

    .strength-bar.weak {
        background-color: #F53838;
    }

    .strength-bar.medium {
        background-color: #D1C700;
    }

    .strength-bar.strong {
        background-color: #77dd77;
    }

    #strength-text.weak {
        color: #F53838;
    }

    #strength-text.medium {
        color: #D1C700;
    }

    #strength-text.strong {
        color: #77dd77;
    }

    /* Style date input calendar icon to match password eye icon */
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: opacity(0.65);
        cursor: pointer;
    }

    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        filter: opacity(0.8);
    }

    /* Prevent eye icon from moving when clicked */
    #toggle-password {
        min-width: 44px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        vertical-align: middle !important;
    }

    #toggle-password:active,
    #toggle-password:focus {
        transform: none !important;
        box-shadow: none !important;
    }

    #toggle-password i {
        width: 20px !important;
        text-align: center !important;
        display: inline-block !important;
        line-height: 1 !important;
        vertical-align: middle !important;
        padding-top: 2px !important;
    }

    /* Custom scrollbar styling to match theme */
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    ::-webkit-scrollbar-track {
        background: #1a1a1a;
        border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb {
        background: #17a2b8;
        border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #138496;
    }

    /* Firefox scrollbar */
    * {
        scrollbar-width: thin;
        scrollbar-color: #17a2b8 #1a1a1a;
    }
</style>
@endpush

@section('content')
    @include('partials.home-content')

    <div class="position-fixed top-0 start-0 w-100 h-100"
        style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
        <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
            <div class="container" style="max-width: 450px;">
                <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center position-relative py-3">
                    <h4 class="mb-0">Register</h4>
                    <a href="{{ route('home') }}"
                        class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3"
                        aria-label="Close"></a>
                </div>
                <div class="card-body p-4">
                    @php
                        $selectedBarangay = old('barangay');
                        $purokOptions = match ($selectedBarangay) {
                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                            default => [],
                        };
                    @endphp
                    <form method="POST" action="{{ route('register') }}" class="registration-form">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label fw-medium">Gender <span
                                    class="text-danger">*</span></label>
                            <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="register-email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email') }}" placeholder="Enter your email address">
                            <div id="email-feedback" class="mt-1" style="font-size: 0.875rem;"></div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number" required>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-medium">Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                rows="2"
                                placeholder="Enter your full address" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="barangay" class="form-label fw-medium">Barangay <span
                                    class="text-danger">*</span></label>
                            <select name="barangay" class="form-control @error('barangay') is-invalid @enderror"
                                data-role="barangay" required>
                                <option value="" disabled selected>Select Barangay</option>
                                <option value="Barangay 11" {{ $selectedBarangay === 'Barangay 11' ? 'selected' : '' }}>
                                    Barangay 11</option>
                                <option value="Barangay 12" {{ $selectedBarangay === 'Barangay 12' ? 'selected' : '' }}>
                                    Barangay 12</option>
                                <option value="Other" {{ $selectedBarangay === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('barangay')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 {{ $selectedBarangay === 'Other' ? '' : 'd-none' }}"
                            data-role="barangay-other-group">
                            <label for="barangay_other" class="form-label fw-medium">Specify Barangay <span class="text-danger">*</span></label>
                            <input id="barangay_other" type="text" class="form-control @error('barangay_other') is-invalid @enderror" name="barangay_other" value="{{ old('barangay_other') }}" data-role="barangay-other" placeholder="Enter your barangay" required>
                            @error('barangay_other')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 {{ in_array($selectedBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}"
                            data-role="purok-group">
                            <label for="purok" class="form-label fw-medium">Purok <span
                                    class="text-danger">*</span></label>
                            <select name="purok" class="form-control @error('purok') is-invalid @enderror"
                                data-role="purok" data-selected="{{ old('purok') }}">
                                <option value="" disabled selected>Select Purok</option>
                                @foreach ($purokOptions as $purok)
                                    <option value="{{ $purok }}" {{ old('purok') === $purok ? 'selected' : '' }}>{{ $purok }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purok')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="birth_date" class="form-label fw-medium">Birth Date <span class="text-danger">*</span></label>
                            <input id="birth_date" type="date" class="form-control @error('birth_date') is-invalid @enderror" name="birth_date" value="{{ old('birth_date') }}" data-role="birth-date" max="{{ now()->toDateString() }}" required>
                            @error('birth_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="register-password" class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="register-password" class="form-control @error('password') is-invalid @enderror" required style="border-right: 0;" placeholder="Enter your password">
                                <button class="btn btn-outline-secondary" type="button" id="toggle-password" style="border-left: 0; background: white; border-color: #6c757d !important;">
                                    <i class="fas fa-eye text-muted"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="password-strength-indicator mt-2">
                                <div class="d-flex gap-1 mb-1">
                                    <div class="strength-bar" id="strength-bar-1"></div>
                                    <div class="strength-bar" id="strength-bar-2"></div>
                                    <div class="strength-bar" id="strength-bar-3"></div>
                                </div>
                                <small id="strength-text" class="text-muted"></small>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary w-100 text-white">Register</button>
                        </form>
                        <div class="text-center mt-3">
                            <small>Already have an account? <a href="{{ route('login') }}">Login here</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Barangay and Purok Logic ---
            const barangayPurokMap = {
                'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3']
            };

            const registrationForms = document.querySelectorAll('.registration-form');
            registrationForms.forEach((form) => {
                const barangaySelect = form.querySelector('[data-role="barangay"]');
                const barangayOtherGroup = form.querySelector('[data-role="barangay-other-group"]');
                const barangayOtherInput = form.querySelector('[data-role="barangay-other"]');
                const purokGroup = form.querySelector('[data-role="purok-group"]');
                const purokSelect = form.querySelector('[data-role="purok"]');

                const updatePurokOptions = (barangay) => {
                    if (!purokSelect) return;
                    const previouslySelected = purokSelect.getAttribute('data-selected');
                    purokSelect.innerHTML = '<option value="" disabled selected>Select Purok</option>';

                    if (!barangayPurokMap[barangay]) {
                        purokSelect.removeAttribute('required');
                        purokSelect.setAttribute('data-selected', '');
                        return;
                    }

                    barangayPurokMap[barangay].forEach((purok) => {
                        const option = document.createElement('option');
                        option.value = purok;
                        option.textContent = purok;
                        if (previouslySelected === purok) {
                            option.selected = true;
                        }
                        purokSelect.appendChild(option);
                    });
                    purokSelect.setAttribute('required', 'required');
                };

                const handleBarangayChange = () => {
                    const selectedBarangay = barangaySelect ? barangaySelect.value : '';

                    if (barangayOtherGroup && barangayOtherInput) {
                        if (selectedBarangay === 'Other') {
                            barangayOtherGroup.classList.remove('d-none');
                            barangayOtherInput.setAttribute('required', 'required');
                        } else {
                            barangayOtherGroup.classList.add('d-none');
                            barangayOtherInput.removeAttribute('required');
                        }
                    }

                    if (purokGroup && purokSelect) {
                        if (barangayPurokMap[selectedBarangay]) {
                            purokGroup.classList.remove('d-none');
                            updatePurokOptions(selectedBarangay);
                        } else {
                            purokGroup.classList.add('d-none');
                            purokSelect.removeAttribute('required');
                            purokSelect.value = '';
                            purokSelect.setAttribute('data-selected', '');
                        }
                    }
                };

                if (barangaySelect) {
                    barangaySelect.addEventListener('change', () => {
                        if (purokSelect) {
                            purokSelect.setAttribute('data-selected', '');
                        }
                        handleBarangayChange();
                    });
                    handleBarangayChange();
                }
            });

            // --- Validation Logic ---
            const form = document.querySelector('.registration-form');
            const passwordInput = document.getElementById('register-password');
            const emailInput = document.getElementById('register-email');
            const nameInput = form.querySelector('input[name="name"]');
            const phoneInput = form.querySelector('input[name="phone"]');

            // Password Visibility Toggle
            const togglePasswordBtn = document.getElementById('toggle-password');
            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    const icon = this.querySelector('i');
                    if (icon.classList.contains('fa-eye')) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }

            // Password Strength Indicator
            const strengthBar1 = document.getElementById('strength-bar-1');
            const strengthBar2 = document.getElementById('strength-bar-2');
            const strengthBar3 = document.getElementById('strength-bar-3');
            const strengthText = document.getElementById('strength-text');

            const calculatePasswordStrength = (password) => {
                if (!password) return 0;
                
                let strength = 0;
                const checks = {
                    length: password.length >= 8,
                    lowercase: /[a-z]/.test(password),
                    uppercase: /[A-Z]/.test(password),
                    number: /\d/.test(password),
                    special: /[!@#$%^&*()_+\-=[\]{}|,.<>/?]/.test(password)
                };

                // Weak: 8+ chars
                if (checks.length) strength = 1;
                
                // Medium: 8+ chars + uppercase + lowercase
                if (checks.length && checks.lowercase && checks.uppercase) strength = 2;
                
                // Strong: 8+ chars + uppercase + lowercase + number + special
                if (checks.length && checks.lowercase && checks.uppercase && checks.number && checks.special) strength = 3;

                return strength;
            };

            const updatePasswordStrength = () => {
                const password = passwordInput.value;
                const strength = calculatePasswordStrength(password);

                // Reset all bars
                [strengthBar1, strengthBar2, strengthBar3].forEach(bar => {
                    bar.classList.remove('weak', 'medium', 'strong');
                });
                strengthText.className = '';

                if (strength === 0) {
                    strengthText.textContent = '';
                } else if (strength === 1) {
                    strengthBar1.classList.add('weak');
                    strengthText.textContent = 'Weak';
                    strengthText.classList.add('weak');
                } else if (strength === 2) {
                    strengthBar1.classList.add('medium');
                    strengthBar2.classList.add('medium');
                    strengthText.textContent = 'Medium';
                    strengthText.classList.add('medium');
                } else if (strength === 3) {
                    strengthBar1.classList.add('strong');
                    strengthBar2.classList.add('strong');
                    strengthBar3.classList.add('strong');
                    strengthText.textContent = 'Strong';
                    strengthText.classList.add('strong');
                }
            };

            // Live Email Validation
            let emailCheckTimeout;
            const emailFeedback = document.getElementById('email-feedback');

            const checkEmailAvailability = async () => {
                const email = emailInput.value.trim();
                
                if (!email || !email.includes('@')) {
                    emailFeedback.innerHTML = '';
                    emailInput.classList.remove('is-invalid', 'is-valid');
                    return;
                }

                try {
                    const response = await fetch('/check-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ email })
                    });

                    const data = await response.json();

                    if (data.available) {
                        emailInput.classList.remove('is-invalid');
                        emailInput.classList.add('is-valid');
                        emailFeedback.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Email is available</span>';
                    } else {
                        emailInput.classList.remove('is-valid');
                        emailInput.classList.add('is-invalid');
                        emailFeedback.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> ' + data.message + '</span>';
                    }
                } catch (error) {
                    console.error('Email check error:', error);
                }
            };

            const clearClientErrors = () => {
                const clientErrors = form.querySelectorAll('.invalid-feedback.js-register-error');
                clientErrors.forEach((el) => el.remove());
                if (nameInput) nameInput.classList.remove('is-invalid');
                if (phoneInput) phoneInput.classList.remove('is-invalid');
                if (passwordInput) passwordInput.classList.remove('is-invalid');
            };

            const appendError = (input, message) => {
                if (!input) return;
                input.classList.add('is-invalid');
                const div = document.createElement('div');
                div.className = 'invalid-feedback js-register-error d-block';
                div.textContent = message;
                const parent = input.closest('.input-group') || input;
                if (parent.nextElementSibling && parent.nextElementSibling.classList.contains('invalid-feedback')) {
                    // Already has error
                } else {
                    parent.insertAdjacentElement('afterend', div);
                }
            };

            const validateNameAndPhone = () => {
                // Validation removed - relying on HTML5 and server-side validation only
                return true;
            };

            // Event Listeners
            if (passwordInput) {
                passwordInput.addEventListener('input', () => {
                    updatePasswordStrength();
                });
            }

            if (emailInput) {
                emailInput.addEventListener('input', () => {
                    clearTimeout(emailCheckTimeout);
                    emailCheckTimeout = setTimeout(checkEmailAvailability, 500);
                });
                emailInput.addEventListener('blur', checkEmailAvailability);
            }
        });
    </script>
@endsection