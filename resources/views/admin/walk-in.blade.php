@extends('admin.layout')

@section('title', 'Walk-In Queue - Admin Panel')
@section('page-title', 'Walk-In Queue')
@section('page-description', 'Manage walk-in patients and queue')

@section('page-styles')
    <style>
        /* Reusing Dashboard Styles for consistency */
        .metric-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            border: 1px solid #edf1f7;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            height: 100%;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
            border-color: #d0e2ff;
        }

        .metric-number {
            font-size: 2.35rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.1;
        }

        .metric-label {
            color: #6b7280;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.35rem;
        }

        .metric-icon-pill {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(var(--color-primary-rgb), 0.1);
            color: var(--color-primary);
        }

        .metric-icon-pill.metric-icon-success {
            background: rgba(var(--color-secondary-rgb), 0.1);
            color: var(--color-secondary);
        }

        .metric-icon-pill.metric-icon-warning {
            background: rgba(var(--color-accent-rgb), 0.1);
            color: var(--color-accent);
        }

        /* Table & Filter Styles */
        .filter-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            margin-bottom: 1.5rem;
        }

        .table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .table-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .table thead th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Custom status colors */
        .status-badge.bg-warning {
            background-color: #FFF52E !important;
            color: #000000 !important;
        }

        .status-badge.bg-success {
            background-color: #77dd77 !important;
            color: #000000 !important;
        }

        .status-badge.bg-danger {
            background-color: #F53838 !important;
            color: #ffffff !important;
        }

        .status-badge.bg-primary {
            background-color: #17a2b8 !important;
            color: #ffffff !important;
        }

        /* Dark mode support */
        body.bg-dark .metric-card,
        body.bg-dark .filter-card,
        body.bg-dark .table-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        body.bg-dark .metric-number {
            color: #e6e6e6;
        }

        body.bg-dark .metric-label {
            color: #b0b0b0;
        }

        body.bg-dark .table-header {
            background: #1a1f24;
            border-color: #2a2f35;
        }

        body.bg-dark .table thead th {
            background: #1a1f24;
            color: #e6e6e6;
        }

        body.bg-dark .table tbody td {
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .table tbody tr:hover {
            background-color: #2a2f35;
        }

        body.bg-dark .form-control,
        body.bg-dark .form-select {
            background: #0f1316;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .form-control:focus,
        body.bg-dark .form-select:focus {
            background: #161b20;
            border-color: #17a2b8;
            color: #e6e6e6;
        }

        body.bg-dark .card-header {
            background: #1a1f24 !important;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .card-header h5 {
            color: #e6e6e6 !important;
        }

        body.bg-dark .modal-content {
            background: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .modal-header,
        body.bg-dark .modal-footer {
            border-color: #2a2f35;
        }

        body.bg-dark .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        body.bg-dark .metric-icon-pill {
            background: rgba(var(--color-primary-rgb), 0.2);
            color: var(--color-primary-light);
        }

        body.bg-dark .metric-icon-pill.metric-icon-success {
            background: rgba(var(--color-secondary-rgb), 0.25);
            color: var(--color-secondary-light);
        }

        body.bg-dark .metric-icon-pill.metric-icon-warning {
            background: rgba(var(--color-accent-rgb), 0.25);
            color: var(--color-accent-light);
        }

        /* Center align action buttons */
        .table tbody td .btn-group {
            display: inline-flex;
            vertical-align: middle;
        }

        .table tbody td .btn-group .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.375rem 0.5rem;
        }

        .table tbody td .btn-group .btn i {
            margin: 0;
            line-height: 1;
        }
    </style>
@endsection

@section('content')
    <!-- Add Walk-In Button -->
    <div class="mb-4 d-flex justify-content-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#walkInModal">
            <i class="fas fa-plus me-2"></i>Add Walk-In Patient
        </button>
    </div>
    <!-- Walk-in Queue Table -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">Walk-In Queue</h5>
            <div class="d-flex gap-2">
                 <input type="text" id="walkInSearch" class="form-control form-control-sm" placeholder="Search walk-ins..." style="width: 200px;">
                <select id="statusFilter" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">All Statuses</option>
                    <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Waiting</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                </select>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#walkInModal">
                    <i class="fas fa-plus me-1"></i> Add Walk-In
                </button>
            </div>
        </div>
        <div class="card-body p-0">
             <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="walkInTable">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Patient Name</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="walkInTableBody">
                        @foreach ($walkIns as $walkIn)
                            <tr>
                                <td>
                                    <div>{{ $walkIn->appointment_date->format('M d, Y') }}</div>
                                    <small
                                        class="text-muted">{{ \Carbon\Carbon::parse($walkIn->appointment_time)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $walkIn->patient_name }}</div>
                                    <small class="text-muted">{{ $walkIn->patient_address }}</small>
                                </td>
                                <td>{{ $walkIn->service_type }}</td>
                                <td>
                                    <span
                                        class="status-badge
                                                                                                                @if ($walkIn->status == 'waiting') bg-warning text-dark
                                                                                                                @elseif($walkIn->status == 'in_progress') bg-primary text-white
                                                                                                                @elseif($walkIn->status == 'completed') bg-success text-white
                                                                                                                @elseif($walkIn->status == 'no_show') bg-danger text-white
                                                                                                                @else bg-secondary text-white @endif">
                                        {{ ucfirst(str_replace('_', ' ', $walkIn->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        @if ($walkIn->status !== 'completed')
                                            <button class="btn btn-outline-success" data-bs-toggle="modal"
                                                data-bs-target="#updateStatusModal{{ $walkIn->id }}" title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#viewModal{{ $walkIn->id }}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>

                                    <!-- Update Status Modal -->
                                    <div class="modal fade" id="updateStatusModal{{ $walkIn->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Status</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.appointment.update', $walkIn) }}">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="waiting" {{ $walkIn->status == 'waiting' ? 'selected' : '' }}>Waiting</option>
                                                                <option value="in_progress" {{ $walkIn->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                                <option value="completed" {{ $walkIn->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                                <option value="no_show" {{ $walkIn->status == 'no_show' ? 'selected' : '' }}>No Show</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes</label>
                                                            <textarea name="notes" class="form-control"
                                                                rows="3">{{ $walkIn->notes }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- View Details Modal -->
                                    <div class="modal fade" id="viewModal{{ $walkIn->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Walk-In Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <th>Patient Name:</th>
                                                            <td>{{ $walkIn->patient_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone:</th>
                                                            <td>{{ $walkIn->patient_phone }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Address:</th>
                                                            <td>{{ $walkIn->patient_address }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Service:</th>
                                                            <td>{{ $walkIn->service_type }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date:</th>
                                                            <td>{{ $walkIn->appointment_date->format('M d, Y') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Time:</th>
                                                            <td>{{ \Carbon\Carbon::parse($walkIn->appointment_time)->format('h:i A') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status:</th>
                                                            <td>
                                                                <span
                                                                    class="status-badge
                                                                                                                                            @if ($walkIn->status == 'waiting') bg-warning text-dark
                                                                                                                                            @elseif($walkIn->status == 'in_progress') bg-primary text-white
                                                                                                                                            @elseif($walkIn->status == 'completed') bg-success text-white
                                                                                                                                            @elseif($walkIn->status == 'no_show') bg-danger text-white
                                                                                                                                            @else bg-secondary text-white @endif">
                                                                    {{ ucfirst(str_replace('_', ' ', $walkIn->status)) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @if ($walkIn->notes)
                                                            <tr>
                                                                <th>Notes:</th>
                                                                <td>{{ $walkIn->notes }}</td>
                                                            </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
             </div> <!-- End table-responsive -->
        </div>
         <div id="walkInPaginationContainer" class="p-3"></div>
    </div>

    <!-- Add Walk-In Modal -->
    <div class="modal fade" id="walkInModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Walk-In Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.walk-in.store') }}" id="walkInForm">
                    @csrf
                    <input type="hidden" name="user_id" id="selected_patient_id">

                    <div class="modal-body">
                        <!-- Patient Search Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">🔍 Search Existing Patient</label>
                            <input type="text" class="form-control" id="patientSearch"
                                placeholder="Search by name, email, or phone..." autocomplete="off">
                            <div id="searchResults" class="list-group mt-2" style="display: none;"></div>
                            <div id="selectedPatientInfo" class="alert alert-info mt-2" style="display: none;">
                                <strong>Selected Patient:</strong>
                                <div id="selectedPatientDetails"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                                    onclick="clearPatientSelection()">
                                    <i class="fas fa-times"></i> Clear Selection
                                </button>
                            </div>
                        </div>

                        <div class="text-center my-3">
                            <span class="badge bg-secondary">OR</span>
                        </div>

                        <!-- Manual Entry Section -->
                        <div id="manualEntrySection">
                            <label class="form-label fw-bold">✚ Create New Walk-In Patient</label>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_name" class="form-label">Patient Name *</label>
                                    <input type="text" class="form-control" id="patient_name" name="patient_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="patient_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="patient_phone" name="patient_phone">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="patient_address" class="form-label">Address *</label>
                                <textarea class="form-control" id="patient_address" name="patient_address"
                                    rows="2"></textarea>
                            </div>
                        </div>

                        <!-- Service and Notes (Common for both) -->
                        <div class="mb-3">
                            <label for="service_type" class="form-label">Service Type *</label>
                            <select class="form-control" id="service_type" name="service_type" required>
                                <option value="" disabled selected>Select Service</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service }}">{{ $service }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Reason for Visit / Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Enter reason for visit or any additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add to Queue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Patient search with debounce
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.patients.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => console.error('Search error:', error));
            }, 300);
        });

        function displaySearchResults(patients) {
            if (patients.length === 0) {
                searchResults.innerHTML = '<div class="list-group-item text-muted">No patients found</div>';
                searchResults.style.display = 'block';
                return;
            }

            searchResults.innerHTML = patients.map(patient => `
                                        <a href="#" class="list-group-item list-group-item-action" onclick="selectPatient(${patient.id}, '${patient.name}', '${patient.phone || ''}', '${patient.email || ''}', ${patient.age || 'null'}); return false;">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>${patient.name}</strong>
                                                    <div class="small text-muted">
                                                        ${patient.phone ? `📞 ${patient.phone}` : ''} 
                                                        ${patient.email ? `📧 ${patient.email}` : ''}
                                                        ${patient.age ? `👤 Age: ${patient.age}` : ''}
                                                    </div>
                                                </div>
                                                <span class="badge bg-primary">Select</span>
                                            </div>
                                        </a>
                                    `).join('');

            searchResults.style.display = 'block';
        }

        function selectPatient(id, name, phone, email, age) {
            selectedPatientId.value = id;
            selectedPatientDetails.innerHTML = `
                                        <strong>${name}</strong><br>
                                        <small>${phone ? `📞 ${phone}` : ''} ${email ? `📧 ${email}` : ''} ${age ? `👤 Age: ${age}` : ''}</small>
                                    `;
            selectedPatientInfo.style.display = 'block';
            searchResults.style.display = 'none';
            searchInput.value = name;

            // Disable manual entry fields
            document.getElementById('patient_name').disabled = true;
            document.getElementById('patient_phone').disabled = true;
            document.getElementById('patient_address').disabled = true;
            document.getElementById('patient_name').required = false;
            document.getElementById('patient_phone').required = false;
            document.getElementById('patient_address').required = false;
        }

        function clearPatientSelection() {
            selectedPatientId.value = '';
            selectedPatientInfo.style.display = 'none';
            searchInput.value = '';
            searchResults.style.display = 'none';

            // Re-enable manual entry fields
            document.getElementById('patient_name').disabled = false;
            document.getElementById('patient_phone').disabled = false;
            document.getElementById('patient_address').disabled = false;
            document.getElementById('patient_name').required = true;
            document.getElementById('patient_phone').required = true;
            document.getElementById('patient_address').required = true;
        }

        // Reset form when modal is closed
        document.getElementById('walkInModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('walkInForm').reset();
            clearPatientSelection();
        });
    </script>
@endpush