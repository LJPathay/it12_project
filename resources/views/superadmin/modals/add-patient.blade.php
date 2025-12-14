<div class="modal fade" id="addPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('superadmin.user.create') }}" class="superadmin-user-form">
                @csrf
                <input type="hidden" name="role" value="user">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        <div class="invalid-feedback">There should be no number in name</div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
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
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" placeholder="09123456789" required>
                        <div class="invalid-feedback">Format for the number is 09123456789</div>
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required
                            placeholder="Enter complete address">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Barangay <span class="text-danger">*</span></label>
                        <select name="barangay" class="form-control @error('barangay') is-invalid @enderror" required data-role="barangay">
                            <option value="" disabled selected>Select Barangay</option>
                            <option value="Barangay 11" {{ old('barangay') == 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                            <option value="Barangay 12" {{ old('barangay') == 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                            <option value="Other" {{ old('barangay') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('barangay')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 d-none" data-role="barangay-other-group">
                        <label class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                        <input type="text" name="barangay_other" class="form-control @error('barangay_other') is-invalid @enderror"
                            value="{{ old('barangay_other') }}" data-role="barangay-other">
                        @error('barangay_other')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 d-none" data-role="purok-group">
                        <label class="form-label">Purok <span class="text-danger">*</span></label>
                        <select name="purok" class="form-control @error('purok') is-invalid @enderror" data-role="purok">
                             <option value="" disabled selected>Select Purok</option>
                        </select>
                        @error('purok')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                         <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                         <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                             value="{{ old('birth_date') }}" required data-role="birth-date" max="{{ now()->toDateString() }}">
                         @error('birth_date')
                             <div class="invalid-feedback d-block">{{ $message }}</div>
                         @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required id="password-patient">
                            <button class="btn btn-outline-secondary toggle-password-btn" type="button" style="background: white; border-color: #ced4da; border-left: 0;">
                                <i class="fas fa-eye text-muted"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="password-strength-indicator mt-2" id="password-strength-patient" style="display: none;">
                             <div class="d-flex gap-1 mb-1" style="height: 4px;">
                                <div class="strength-bar flex-grow-1 rounded-pill" style="background-color: #e9ecef;"></div>
                                <div class="strength-bar flex-grow-1 rounded-pill" style="background-color: #e9ecef;"></div>
                                <div class="strength-bar flex-grow-1 rounded-pill" style="background-color: #e9ecef;"></div>
                            </div>
                            <small class="strength-text text-muted" style="font-size: 0.75rem;"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>
