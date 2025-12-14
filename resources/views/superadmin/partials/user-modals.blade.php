            <!-- Modals for EACH user are placed here so they are refreshed with AJAX -->
            @foreach($users as $user)
                <!-- Edit Modal -->
                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit {{ ucfirst(in_array($user->role, ['user', 'patient']) ? 'Patient' : $user->role) }} - {{ $user->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            
                            @if(in_array($user->role, ['user', 'patient']))
                                <!-- Patient Edit Form -->
                                <form method="POST"
                                    action="{{ route('superadmin.user.update', ['type' => 'user', 'id' => $user->id]) }}"
                                    class="superadmin-user-form">
                                    @csrf
                                    @php
                                        $editBarangay = old('barangay', $user->barangay);
                                        $editPurokOptions = match ($editBarangay) {
                                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                                            default => [],
                                        };
                                        $editBirthDateRaw = old(
                                            'birth_date',
                                            $user->birth_date ? \Illuminate\Support\Carbon::parse($user->birth_date)->format('Y-m-d') : ''
                                        );
                                    @endphp
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="name{{ $user->id }}" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="gender{{ $user->id }}" class="form-label">Gender <span class="text-danger">*</span></label>
                                            <select class="form-control @error('gender') is-invalid @enderror" id="gender{{ $user->id }}"
                                                name="gender" required>
                                                <option value="" disabled>Select Gender</option>
                                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Prefer not to say</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="email{{ $user->id }}" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $user->phone) }}" placeholder="09123456789" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address <small class="text-muted">(Optional)</small></label>
                                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2"
                                                placeholder="Enter complete address">{{ old('address', $user->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Barangay <span class="text-danger">*</span></label>
                                            <select name="barangay" class="form-control @error('barangay') is-invalid @enderror" required data-role="barangay">
                                                <option value="" disabled selected>Select Barangay</option>
                                                <option value="Barangay 11" {{ old('barangay', $user->barangay) == 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                                                <option value="Barangay 12" {{ old('barangay', $user->barangay) == 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                                                <option value="Other" {{ old('barangay', $user->barangay) == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('barangay')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3 {{ old('barangay', $user->barangay) == 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                                            <label class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                                            <input type="text" name="barangay_other" class="form-control @error('barangay_other') is-invalid @enderror"
                                                value="{{ old('barangay_other', $user->barangay_other) }}" data-role="barangay-other">
                                            @error('barangay_other')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3 {{ in_array(old('barangay', $user->barangay), ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                                            <label class="form-label">Purok <span class="text-danger">*</span></label>
                                            <select name="purok" class="form-control @error('purok') is-invalid @enderror" data-role="purok" data-selected="{{ old('purok', $user->purok) }}">
                                                 <option value="" disabled selected>Select Purok</option>
                                                 @foreach($editPurokOptions as $purok)
                                                    <option value="{{ $purok }}" {{ old('purok', $user->purok) == $purok ? 'selected' : '' }}>{{ $purok }}</option>
                                                 @endforeach
                                            </select>
                                            @error('purok')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                             <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                                             <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                                                 value="{{ $editBirthDateRaw }}" required data-role="birth-date" max="{{ now()->toDateString() }}">
                                             @error('birth_date')
                                                 <div class="invalid-feedback">{{ $message }}</div>
                                             @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password{{ $user->id }}" class="form-label">Password (leave blank to keep current)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                    id="password{{ $user->id }}" name="password" placeholder="New Password">
                                                <button class="btn btn-outline-secondary toggle-password-btn" type="button" style="background: white; border-color: #ced4da; border-left: 0;">
                                                    <i class="fas fa-eye text-muted"></i>
                                                </button>
                                            </div>
                                            <div class="form-text text-muted">Must be at least 8 characters.</div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            @else
                                <!-- Admin Edit Form -->
                                <form method="POST"
                                    action="{{ route('superadmin.user.update', ['type' => $user->role, 'id' => $user->id]) }}"
                                    class="superadmin-user-form">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="name{{ $user->id }}" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="email{{ $user->id }}" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Role Display Only -->
                                        <div class="mb-3">
                                            <label class="form-label">Role</label>
                                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password{{ $user->id }}" class="form-label">Password (leave blank to keep current)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                    id="password{{ $user->id }}" name="password" placeholder="New Password">
                                                <button class="btn btn-outline-secondary toggle-password-btn" type="button" style="background: white; border-color: #ced4da; border-left: 0;">
                                                    <i class="fas fa-eye text-muted"></i>
                                                </button>
                                            </div>
                                            <div class="form-text text-muted">Must be at least 8 characters.</div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- View User Modal -->
                <div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">User Details - {{ $user->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> {{ $user->name }}</p>
                                        <p><strong>Email:</strong> {{ $user->email }}</p>
                                        <p><strong>Role:</strong>
                                            <span class="status-badge 
                                                                        @if($user->role == 'superadmin') status-superadmin
                                                                        @elseif($user->role == 'admin') status-admin
                                                                        @else status-user
                                                                        @endif">
                                                {{ ucfirst(in_array($user->role, ['user', 'patient']) ? 'Patient' : $user->role) }}
                                            </span>
                                        </p>
                                        <p><strong>Gender:</strong> {{ ucfirst($user->gender ?? 'N/A') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        @if(in_array($user->role, ['user', 'patient']))
                                            <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                                            <p><strong>Barangay:</strong>
                                                @if($user->barangay === 'Other')
                                                    {{ $user->barangay_other ?? 'Other' }}
                                                @else
                                                    {{ $user->barangay ?? 'N/A' }}
                                                @endif
                                            </p>
                                            <p><strong>Purok:</strong> {{ $user->purok ?? 'N/A' }}</p>
                                            <p><strong>Birth Date:</strong>
                                                {{ $user->birth_date ? \Illuminate\Support\Carbon::parse($user->birth_date)->format('M d, Y') : 'N/A' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                @if(in_array($user->role, ['user', 'patient']))
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <p><strong>Registered:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $user->id }}" data-bs-dismiss="modal">
                                    <i class="fas fa-edit me-1"></i> Edit User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Archive User Modal -->
                @if($user->id !== Auth::id())
                    <div class="modal fade" id="archiveUserModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Archive User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to archive <strong>{{ $user->name }}</strong>?</p>
                                    <p class="text-muted">This action will remove the user from the active list but preserve their data.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form method="POST"
                                        action="{{ route('superadmin.user.delete', ['type' => $user->role, 'id' => $user->id]) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-archive me-1"></i> Archive User
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
