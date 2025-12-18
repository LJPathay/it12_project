@extends('admin.layout')

@section('title', 'Malasakit | Walk-In Queue')
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
            background-color: #D1D100 !important;
            color: #ffffff !important;
        }

        .status-badge.bg-success {
            background-color: #00D100 !important;
            color: #ffffff !important;
        }

        .status-badge.bg-danger {
            background-color: #D10000 !important;
            color: #ffffff !important;
        }

        .status-badge.bg-primary {
            background-color: #009fb1 !important;
            color: #000000 !important;
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
            border-color: #009fb1;
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

        /* Priority & Queue Board Styles */
        .queue-number {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 800;
            font-size: 1.1rem;
            color: #0d6efd;
            background: #f0f7ff;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #cfe2ff;
        }

        .emergency-row {
            background-color: rgba(220, 53, 69, 0.05) !important;
        }

        .emergency-pulse {
            animation: pulse-red 2s infinite;
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            border-radius: 50%;
        }

        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }

        .priority-badge {
            font-size: 0.7rem;
            text-transform: uppercase;
            padding: 3px 8px;
            font-weight: 700;
            border-radius: 4px;
        }
        
        .badge-regular { background: #e9ecef; color: #495057; }
        .badge-priority { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-emergency { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
@endsection

@section('content')
    <!-- Queue Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">Total Today</div>
                        <div class="metric-number">{{ $todayWalkIns }}</div>
                    </div>
                    <div class="metric-icon-pill">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">Waiting</div>
                        <div class="metric-number text-warning">{{ $todayWaiting }}</div>
                    </div>
                    <div class="metric-icon-pill metric-icon-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">In Progress</div>
                        <div class="metric-number text-primary">{{ $todayInProgress }}</div>
                    </div>
                    <div class="metric-icon-pill">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">Completed Today</div>
                        <div class="metric-number text-success">{{ $todayCompleted }}</div>
                    </div>
                    <div class="metric-icon-pill metric-icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Walk-In Button -->
    <div class="mb-4 d-flex justify-content-end align-items-center gap-3">
        <div class="text-muted small"><i class="fas fa-info-circle me-1"></i> Walk-ins are ordered by time and priority</div>
        <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#walkInModal">
            <i class="fas fa-plus me-2"></i>Add Walk-In Patient
        </button>
    </div>
    <!-- Walk-in Queue Table -->
    <div class="card">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center py-3 gap-3">
            <h5 class="mb-0 fw-bold text-secondary text-nowrap">Walk-In Queue</h5>
            <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0 w-100 w-md-auto">
                 <input type="text" id="walkInSearch" class="form-control form-control-sm flex-grow-1" placeholder="Search walk-ins..." style="min-width: 150px;">
                <select id="statusFilter" class="form-select form-select-sm" style="width: auto; min-width: 120px;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <select id="priorityFilter" class="form-select form-select-sm" style="width: auto; min-width: 120px;">
                    <option value="">All Priorities</option>
                    <option value="regular">Regular</option>
                    <option value="priority">Priority</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
             <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="walkInTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Queue #</th>
                            <th>Patient / Priority</th>
                            <th>Service</th>
                            <th>Arrival Time</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="walkInTableBody">
                        @php $queueCounter = []; @endphp
                        @foreach ($walkIns as $walkIn)
                            @php
                                $dateKey = $walkIn->appointment_date->format('Y-m-d');
                                $queueCounter[$dateKey] = ($queueCounter[$dateKey] ?? 0) + 1;
                                $displayQueueNum = 'W-' . str_pad($queueCounter[$dateKey], 3, '0', STR_PAD_LEFT);
                                
                                $priorityClass = match($walkIn->priority) {
                                    'emergency' => 'emergency-row',
                                    default => '',
                                };
                            @endphp
                            <tr class="{{ $priorityClass }}">
                                <td class="ps-3">
                                    <span class="queue-number">{{ $displayQueueNum }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($walkIn->priority === 'emergency')
                                            <div class="emergency-pulse me-2" style="width: 8px; height: 8px; background: #dc3545;"></div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $walkIn->patient_name }}</div>
                                            <span class="priority-badge badge-{{ $walkIn->priority ?? 'regular' }}">
                                                {{ $walkIn->priority ?? 'Regular' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $walkIn->service_type }}</span>
                                </td>
                                <td>
                                    <div class="small fw-medium">{{ \Carbon\Carbon::parse($walkIn->appointment_time)->format('h:i A') }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $walkIn->appointment_date->format('M d') }}</div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="status-badge
                                                                                                                @if ($walkIn->status == 'pending') bg-warning text-dark
                                                                                                                @elseif($walkIn->status == 'in_progress') bg-primary text-dark
                                                                                                                @elseif($walkIn->status == 'completed') bg-success text-dark
                                                                                                                @elseif($walkIn->status == 'cancelled' || $walkIn->status == 'no_show') bg-danger text-dark
                                                                                                                @else bg-secondary text-dark @endif">
                                        {{ ucfirst(str_replace('_', ' ', $walkIn->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <!-- Main Workflow Action -->
                                        @if ($walkIn->status === 'pending')
                                            <form method="POST" action="{{ route('admin.appointment.update', $walkIn) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm" title="Start Serving">
                                                    <i class="fas fa-play me-1"></i> Start
                                                </button>
                                            </form>
                                        @elseif($walkIn->status === 'in_progress')
                                            <form method="POST" action="{{ route('admin.appointment.update', $walkIn) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-success px-3 shadow-sm" title="Finish Serving">
                                                    <i class="fas fa-check me-1"></i> Finish
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Direct Access Actions -->
                                        <button class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="modal" 
                                            data-bs-target="#viewModal{{ $walkIn->id }}" title="View Details">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>
                                    </div>
                                </td><!-- View Details Modal -->
                                    <div class="modal fade" id="viewModal{{ $walkIn->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Walk-In Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <!-- Header: Name & Status -->
                                                    <div class="d-flex align-items-start justify-content-between mb-4 pb-3 border-bottom">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 64px; height: 64px; font-size: 1.5rem;">
                                                                {{ substr($walkIn->patient_name, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <h4 class="fw-bold mb-1">{{ $walkIn->patient_name }}</h4>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge rounded-pill px-2 py-1 fw-normal d-flex align-items-center gap-1
                                                                        @if ($walkIn->status == 'pending') bg-warning text-dark
                                                                        @elseif($walkIn->status == 'in_progress') bg-primary text-white
                                                                        @elseif($walkIn->status == 'completed') bg-success text-white
                                                                        @elseif($walkIn->status == 'no_show') bg-danger text-white
                                                                        @else bg-secondary text-white @endif">
                                                                        @if($walkIn->status == 'pending') <i class="fas fa-clock fa-xs"></i>
                                                                        @elseif($walkIn->status == 'in_progress') <i class="fas fa-spinner fa-xs"></i>
                                                                        @elseif($walkIn->status == 'completed') <i class="fas fa-check fa-xs"></i>
                                                                        @elseif($walkIn->status == 'no_show') <i class="fas fa-times fa-xs"></i>
                                                                        @endif
                                                                        {{ ucfirst(str_replace('_', ' ', $walkIn->status)) }}
                                                                    </span>
                                                                    <span class="text-muted small border-start ps-2 ms-1">Walk-In Patient</span>
                                                                    <span class="badge badge-{{ $walkIn->priority ?? 'regular' }} ms-2" style="font-size: 0.65rem;">
                                                                        {{ strtoupper($walkIn->priority ?? 'Regular') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Details Grid -->
                                                    <div class="row g-4 mb-4">
                                                        <!-- Contact Information -->
                                                        <div class="col-md-6 border-end">
                                                            <h6 class="text-uppercase text-secondary small fw-bold mb-3 border-bottom pb-2">Contact Information</h6>
                                                            <div class="row mb-2">
                                                                <div class="col-4 text-muted small">Phone</div>
                                                                <div class="col-8 fw-medium">{{ $walkIn->patient_phone }}</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-4 text-muted small">Address</div>
                                                                <div class="col-8 fw-medium">{{ $walkIn->patient_address }}</div>
                                                            </div>
                                                        </div>

                                                        <!-- Visit Details -->
                                                        <div class="col-md-6">
                                                            <h6 class="text-uppercase text-secondary small fw-bold mb-3 border-bottom pb-2">Visit Details</h6>
                                                            <div class="row mb-2">
                                                                <div class="col-4 text-muted small">Service</div>
                                                                <div class="col-8 fw-medium text-primary">{{ $walkIn->service_type }}</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-4 text-muted small">Date & Time</div>
                                                                <div class="col-8 fw-medium">
                                                                    {{ $walkIn->appointment_date->format('M d, Y') }}
                                                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($walkIn->appointment_time)->format('h:i A') }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Notes Section -->
                                                    @if ($walkIn->notes)
                                                        <div class="bg-light rounded-3 p-3 border">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <label class="text-uppercase text-secondary small fw-bold mb-0">Visit Notes</label>
                                                                <span class="badge bg-secondary opacity-50 text-white" style="font-size: 0.65rem;">READ ONLY</span>
                                                            </div>
                                                            <div class="text-dark fst-italic">"{{ $walkIn->notes }}"</div>
                                                        </div>
                                                    @else
                                                        <div class="bg-light rounded-3 p-3 border text-center text-muted small">
                                                            No notes recorded for this visit.
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Actions Footer -->
                                                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-outline-secondary border-0" data-bs-dismiss="modal">Close</button>
                                                    @if ($walkIn->status !== 'completed')
                                                        <button type="button" class="btn btn-primary px-4" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#updateStatusModal{{ $walkIn->id }}"
                                                            onclick="$('#viewModal{{ $walkIn->id }}').modal('hide')" 
                                                        >
                                                            <i class="fas fa-edit me-2"></i> Edit Record
                                                        </button>
                                                    @endif
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
                            <label class="form-label fw-bold">✚ Create Walk-In Patient</label>

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
                            <label for="priority" class="form-label">Priority Level *</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="regular" selected>Regular</option>
                                <option value="priority">Priority (Senior/PWD)</option>
                                <option value="emergency">Emergency</option>
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
        // Global variables for search
        const patientSearchInput = document.getElementById('patientSearch');
        const patientSearchResults = document.getElementById('searchResults');
        const selectedPatientIdInput = document.getElementById('selected_patient_id');
        const selectedPatientInfoDiv = document.getElementById('selectedPatientInfo');
        const selectedPatientDetailsDiv = document.getElementById('selectedPatientDetails');
        let searchTimeout;

        // Functions must be in global scope for onclick attributes
        function displaySearchResults(patients) {
            if (!patientSearchResults) return;

            if (patients.length === 0) {
                patientSearchResults.innerHTML = '<div class="list-group-item text-muted">No patients found</div>';
                patientSearchResults.style.display = 'block';
                return;
            }

            patientSearchResults.innerHTML = patients.map(patient => `
                <a href="#" class="list-group-item list-group-item-action" 
                   onclick="selectPatient(${patient.id}, '${patient.name.replace(/'/g, "\\'")}', '${(patient.phone || '').replace(/'/g, "\\'")}', '${(patient.email || '').replace(/'/g, "\\'")}', ${patient.age || 'null'}); return false;">
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

            patientSearchResults.style.display = 'block';
        }

        function selectPatient(id, name, phone, email, age) {
            if (selectedPatientIdInput) selectedPatientIdInput.value = id;
            if (selectedPatientDetailsDiv) {
                selectedPatientDetailsDiv.innerHTML = `
                    <strong>${name}</strong><br>
                    <small>${phone ? `📞 ${phone}` : ''} ${email ? `📧 ${email}` : ''} ${age ? `👤 Age: ${age}` : ''}</small>
                `;
            }
            if (selectedPatientInfoDiv) selectedPatientInfoDiv.style.display = 'block';
            if (patientSearchResults) patientSearchResults.style.display = 'none';
            if (patientSearchInput) patientSearchInput.value = name;

            // Disable manual entry fields
            const manualFields = ['patient_name', 'patient_phone', 'patient_address'];
            manualFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.disabled = true;
                    el.required = false;
                }
            });
        }

        function clearPatientSelection() {
            if (selectedPatientIdInput) selectedPatientIdInput.value = '';
            if (selectedPatientInfoDiv) selectedPatientInfoDiv.style.display = 'none';
            if (patientSearchInput) {
                patientSearchInput.value = '';
                patientSearchInput.focus();
            }
            if (patientSearchResults) patientSearchResults.style.display = 'none';

            // Re-enable manual entry fields
            const manualFields = ['patient_name', 'patient_phone', 'patient_address'];
            manualFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.disabled = false;
                    el.required = true;
                }
            });
        }

        // Event Listeners
        if (patientSearchInput) {
            patientSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    if (patientSearchResults) patientSearchResults.style.display = 'none';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('admin.patients.search') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            displaySearchResults(data);
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            if (patientSearchResults) {
                                patientSearchResults.innerHTML = '<div class="list-group-item text-danger small">Error connecting to server</div>';
                                patientSearchResults.style.display = 'block';
                            }
                        });
                }, 300);
            });
        }

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (patientSearchResults && !patientSearchInput?.contains(e.target) && !patientSearchResults.contains(e.target)) {
                patientSearchResults.style.display = 'none';
            }
        });

        // Reset form when modal is closed
        document.getElementById('walkInModal')?.addEventListener('hidden.bs.modal', function () {
            document.getElementById('walkInForm')?.reset();
            clearPatientSelection();
        });

        const runFilters = () => {
            const searchText = document.getElementById('walkInSearch')?.value.toLowerCase();
            const statusValue = document.getElementById('statusFilter')?.value.toLowerCase();
            const priorityValue = document.getElementById('priorityFilter')?.value.toLowerCase();
            const rows = document.querySelectorAll('#walkInTableBody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const statusBadge = row.querySelector('.status-badge');
                const rowStatus = statusBadge ? statusBadge.innerText.trim().toLowerCase().replace(' ', '_') : '';
                
                const priorityBadge = row.querySelector('.priority-badge');
                const rowPriority = priorityBadge ? priorityBadge.innerText.trim().toLowerCase() : '';

                const matchesText = !searchText || text.includes(searchText);
                const matchesStatus = !statusValue || rowStatus === statusValue;
                const matchesPriority = !priorityValue || rowPriority === priorityValue;

                row.style.display = (matchesText && matchesStatus && matchesPriority) ? '' : 'none';
            });
        };

        ['walkInSearch', 'statusFilter', 'priorityFilter'].forEach(id => {
            document.getElementById(id)?.addEventListener(id === 'walkInSearch' ? 'keyup' : 'change', runFilters);
        });
    </script>
@endpush