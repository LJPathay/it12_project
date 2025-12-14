    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const clearBtn = document.getElementById('clearFiltersBtn');
            const tableContainer = document.querySelector('.table-container');
            const tableBody = document.querySelector('.users-table tbody');
            const totalCountSpan = document.getElementById('totalUsersCount');
            
            function filterUsers() {
                const searchText = searchInput.value.toLowerCase().trim();
                const selectedRole = roleFilter.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    // Extract data from columns
                    const name = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase().trim() || '';
                    const email = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase().trim() || '';
                    // Get raw role text from hidden inputs or infer from badge class/text if needed.
                    // Assuming the badge text contains the role e.g., "Patient", "Admin", "Superadmin"
                    const roleBadge = row.querySelector('.status-badge');
                    const roleText = roleBadge ? roleBadge.textContent.toLowerCase().trim() : '';

                    // Map role filter values to display text matching
                    // Filter value: 'user', 'admin'
                    // Display text: 'patient', 'admin', 'super admin'
                    let roleMatch = true;
                    if (selectedRole) {
                        if (selectedRole === 'user') {
                            roleMatch = roleText === 'patient';
                        } else if (selectedRole === 'admin') {
                            roleMatch = roleText === 'admin';
                        } else {
                            // strictly match others if any
                            roleMatch = roleText.includes(selectedRole);
                        }
                    }

                    const searchMatch = name.includes(searchText) || email.includes(searchText);

                    if (roleMatch && searchMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update count
                if (totalCountSpan) {
                    totalCountSpan.textContent = visibleCount;
                }

                // Show/Hide "No users found" message if needed (optional enhancement)
                // Existing "No users found" div is outside the table, handled by backend usually, 
                // but for client side we might want to toggle a hidden row or div.
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterUsers);
            }

            if (roleFilter) {
                roleFilter.addEventListener('change', filterUsers);
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    searchInput.value = '';
                    roleFilter.value = '';
                    filterUsers();
                });
            }
        });

        // Generic confirmation modal for destructive actions
        (function () {
            const modalEl = document.getElementById('confirmActionModal');
            if (!modalEl || typeof bootstrap === 'undefined') {
                return;
            }

            const modal = new bootstrap.Modal(modalEl);
            const titleEl = document.getElementById('confirmActionTitle');
            const messageEl = document.getElementById('confirmActionMessage');
            const confirmBtn = document.getElementById('confirmActionBtn');

            let pendingForm = null;

            document.querySelectorAll('[data-confirm]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form');
                    if (!form) return;
                    pendingForm = form;

                    if (titleEl) {
                        titleEl.textContent = btn.getAttribute('data-confirm-title') || 'Confirm Action';
                    }
                    if (messageEl) {
                        messageEl.textContent = btn.getAttribute('data-confirm-message') || 'Are you sure you want to proceed?';
                    }

                    modal.show();
                });
            });

            if (confirmBtn) {
                confirmBtn.addEventListener('click', () => {
                    if (pendingForm) {
                        pendingForm.submit();
                        pendingForm = null;
                    }
                    modal.hide();
                });
            }
        })();

        const barangayPurokMap = {
            'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
            'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3'],
        };

        function initSuperadminUserForms(scope = document) {
            const forms = scope.querySelectorAll('.superadmin-user-form');

            forms.forEach((form) => {
                const roleSelect = form.querySelector('[data-role="user-role"]');
                const barangaySelect = form.querySelector('[data-role="barangay"]');
                const barangayOtherGroup = form.querySelector('[data-role="barangay-other-group"]');
                const barangayOtherInput = form.querySelector('[data-role="barangay-other"]');
                const purokGroup = form.querySelector('[data-role="purok-group"]');
                const purokSelect = form.querySelector('[data-role="purok"]');
                const birthDateInput = form.querySelector('[data-role="birth-date"]');
                const nameInput = form.querySelector('input[name="name"]');
                const phoneInput = form.querySelector('input[name="phone"]');
                const passwordInput = form.querySelector('input[name="password"]');
                const passwordConfirmInput = form.querySelector('input[name="password_confirmation"]');
                const registerPasswordToggleBtn = form.querySelector('[data-role="register-password-toggle-btn"]');
                const registerPasswordToggleIcon = form.querySelector('[data-role="register-password-toggle-icon"]');

                const barangayPurokMap = {
                    'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                    'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3'],
                };

                const updatePurokOptions = (barangay) => {
                    if (!purokSelect) {
                        return;
                    }

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
                            barangayOtherInput.value = '';
                        }
                    }

                    if (purokGroup) {
                        if (selectedBarangay === 'Barangay 11' || selectedBarangay === 'Barangay 12') {
                            purokGroup.classList.remove('d-none');
                            updatePurokOptions(selectedBarangay);
                        } else {
                            purokGroup.classList.add('d-none');
                            purokSelect.removeAttribute('required');
                            purokSelect.innerHTML = '<option value="" disabled selected>Select Purok</option>';
                        }
                    }
                };

                // Initial state
                handleBarangayChange();
                updatePurokOptions();

                // Event Listeners
                if (roleSelect) {
                    roleSelect.addEventListener('change', () => {
                        handleBarangayChange();
                        updatePurokOptions();
                    });
                }
                if (barangaySelect) {
                    barangaySelect.addEventListener('change', handleBarangayChange);
                }

                // Handle form submission - let Laravel handle validation
                form.addEventListener('submit', function (event) {
                    // Allow form to submit normally to get Laravel validation errors
                    // No need to prevent default or use Bootstrap validation
                    form.classList.add('was-validated');
                }, false);

                // Real-time validation for name field
                if (nameInput) {
                    nameInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');

                        if (value && /\d/.test(value)) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'There should be no number in name';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length > 0) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for phone field
                if (phoneInput) {
                    phoneInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');

                        if (value && !/^09\d{9}$/.test(value)) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Format for the number is 09123456789';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length > 0) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for email field
                const emailInput = form.querySelector('input[name="email"]');
                if (emailInput) {
                    emailInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');

                        if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Please enter a valid email address.';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length > 0) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for password field
                if (passwordInput) {
                    passwordInput.addEventListener('input', function () {
                        const value = this.value;
                        const feedbackDiv = this.parentNode.parentNode.querySelector('.invalid-feedback');

                        if (value.length > 0 && value.length < 8) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Needs 8 chars with uppercase, lowercase, and number';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length > 0 && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/.test(value)) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Needs 8 chars with uppercase, lowercase, and number';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length >= 8 && /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/.test(value)) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for birth date field
                if (birthDateInput) {
                    birthDateInput.addEventListener('input', function () {
                        const value = this.value;
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');
                        const today = new Date().toISOString().split('T')[0];

                        if (value && value >= today) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Birth date must be in the past.';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value && value < today) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for password confirmation
                if (passwordConfirmInput) {
                    passwordConfirmInput.addEventListener('input', function () {
                        const value = this.value;
                        const passwordValue = passwordInput ? passwordInput.value : '';
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');

                        if (value && value !== passwordValue) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'The password confirmation does not match.';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value && value === passwordValue) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Password toggle functionality
                if (registerPasswordToggleBtn && passwordInput) {
                    registerPasswordToggleBtn.addEventListener('click', function () {
                        const showing = passwordInput.type === 'text';
                        const show = !showing;

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

                if (birthDateInput) {
                    birthDateInput.addEventListener('change', () => { });
                    birthDateInput.addEventListener('keyup', () => { });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initSuperadminUserForms();

            // Sync table with dark mode on page load
            const syncTableDark = () => {
                const isDark = document.body.classList.contains('bg-dark');
                const table = document.querySelector('.users-table');
                if (table) {
                    table.classList.toggle('table-dark', isDark);
                }
            };

            // Sync on load
            syncTableDark();

            // Watch for theme changes
            const observer = new MutationObserver(() => {
                syncTableDark();
            });
            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Password Visibility Toggle (Delegated)
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.toggle-password-btn');
                if (btn) {
                    const input = btn.parentElement.querySelector('input');
                    if (input) {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        
                        const icon = btn.querySelector('i');
                        if (type === 'text') {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    }
                }
            });

            // 2. Password Strength Meter Helper
            function initStrengthMeter(inputId, indicatorId) {
                const passwordInput = document.getElementById(inputId);
                const strengthIndicator = document.getElementById(indicatorId);
                
                if (passwordInput && strengthIndicator) {
                    const bars = [
                        strengthIndicator.querySelector('.strength-bar:nth-child(1)'),
                        strengthIndicator.querySelector('.strength-bar:nth-child(2)'),
                        strengthIndicator.querySelector('.strength-bar:nth-child(3)')
                    ];
                    const text = strengthIndicator.querySelector('.strength-text');

                    passwordInput.addEventListener('input', function() {
                        const value = this.value;
                        if (value.length > 0) {
                            strengthIndicator.style.display = 'block';
                        } else {
                            strengthIndicator.style.display = 'none';
                            return;
                        }

                        // Strength Calculation (Unified logic)
                        let score = 0;
                        if (value.length >= 8) score++;
                        if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
                        if (/[0-9]/.test(value)) score++;
                        if (/[^a-zA-Z0-9]/.test(value)) score++;

                        // Map score (0-4) to bars (0-3)
                        let activeBars = 0;
                        let color = '#dc3545'; // Red
                        let label = 'Weak';

                        if (score < 2) {
                            activeBars = 1;
                        } else if (score === 2 || score === 3) {
                            activeBars = 2;
                            color = '#ffc107'; // Yellow
                            label = 'Medium';
                        } else if (score >= 4) {
                            activeBars = 3;
                            color = '#198754'; // Green
                            label = 'Strong';
                        }

                        // Update UI
                        text.textContent = label;
                        text.style.color = color;

                        bars.forEach((bar, index) => {
                            if (index < activeBars) {
                                bar.style.backgroundColor = color;
                            } else {
                                bar.style.backgroundColor = '#e9ecef';
                            }
                        });
                    });
                }
            }

            // Initialize for Admin and Patient modals
            initStrengthMeter('password-admin', 'password-strength-admin');
            initStrengthMeter('password-patient', 'password-strength-patient');
        });
    </script>
