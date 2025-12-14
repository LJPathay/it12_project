<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('superadmin.user.create') }}" class="superadmin-user-form">
                @csrf
                <input type="hidden" name="role" value="admin">
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
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required id="password-admin">
                            <button class="btn btn-outline-secondary toggle-password-btn" type="button" style="background: white; border-color: #ced4da; border-left: 0;">
                                <i class="fas fa-eye text-muted"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <!-- Strength Meter for Admin (Optional, reusing generic class/id logic might need adjustment if duplicated IDs) -->
                        <div class="password-strength-indicator mt-2" id="password-strength-admin" style="display: none;">
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
                    <button type="submit" class="btn btn-primary">Add Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
