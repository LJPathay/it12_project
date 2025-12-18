@extends('admin.layout')

@section('title', 'Malasakit | Dashboard')
@section('page-title', 'Dashboard Overview')
@section('page-description', "Welcome back! Here's what's happening today.")

@section('page-styles')
    <style>
        /* Shared Analytics Theme Repetition */
        .analytics-container { max-width: 1400px; }

        /* KPI Cards */
        .kpi-row { display: flex; gap: 1.5rem; margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 0.5rem; }
        .kpi-card {
            flex: 1;
            min-width: 200px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .kpi-label { font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
        .kpi-value { font-size: 2rem; font-weight: 700; color: #1e293b; line-height: 1; }
        .kpi-sub { font-size: 0.8rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.25rem; color: #64748b; }
        
        .text-trend-up { color: #00D100; }
        .text-trend-down { color: #D10000; }
        .text-neutral { color: #94a3b8; }

        /* Chart & Section Cards */
        .chart-section {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; height: 100%;
        }
        .section-header { margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .header-title { font-size: 1.1rem; font-weight: 700; color: #334155; }

        /* Activity List */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-right: 1rem; flex-shrink: 0;
        }
        
        /* Dark Mode */
        body.bg-dark .kpi-card, body.bg-dark .chart-section { background: #1e2124; border-color: #2d3748; }
        body.bg-dark .kpi-value, body.bg-dark .header-title { color: #f1f5f9; }
        body.bg-dark .kpi-label { color: #94a3b8; }
        body.bg-dark .activity-item { border-bottom-color: #2d3748; }

        /* Filter Toggles */
        .btn-filter {
            border: none; background: transparent; color: #94a3b8; font-weight: 600; font-size: 0.85rem; padding: 0.25rem 0.75rem;
        }
        .btn-filter.active { color: #0f172a; text-decoration: underline; text-underline-offset: 4px; }
        body.bg-dark .btn-filter.active { color: #f1f5f9; }

        /* Modal & Info Card Styles (Copied from Appointments) */
        .info-card { background: #f8f9fa; border: 1px solid #e9ecef; transition: all 0.2s ease; }
        .info-label { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; margin-bottom: 0.5rem; }
        .info-value { font-size: 0.95rem; color: #212529; font-weight: 500; }
        
        .status-badge { padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .status-pending { background-color: #D1D100; color: #ffffff; }
        .status-approved { background-color: #00D100; color: #ffffff; }
        .status-completed { background-color: #009fb1; color: #000000; }
        .status-cancelled { background-color: #D10000; color: #ffffff; }
        .status-rescheduled { background-color: #ffc107; color: #000; }

        /* Dark Mode Modal */
        body.bg-dark .info-card { background: #2a2f35; border-color: #3f4751; }
        body.bg-dark .info-label { color: #adb5bd; }
        body.bg-dark .info-value { color: #e9ecef; }
        body.bg-dark .modal-content { background: #1e2124; border-color: #2a2f35; color: #e6e6e6; }
        body.bg-dark .modal-header { border-bottom-color: #2a2f35; }
        body.bg-dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

        /* Timeline Styles */
        .timeline-container { position: relative; padding-left: 1rem; }
        .timeline-item { position: relative; padding-bottom: 1.5rem; display: flex; gap: 1.5rem; }
        .timeline-item:last-child { padding-bottom: 0; }
        
        .timeline-line {
            position: absolute; left: 86px; top: 2rem; bottom: -2rem;
            width: 2px; background-color: #e2e8f0; z-index: 0;
        }
        .timeline-item:last-child .timeline-line { display: none; }
        body.bg-dark .timeline-line { background-color: #2d3748; }

        .timeline-time {
            min-width: 70px; text-align: right; padding-top: 0.35rem; /* Aligned with card title */
        }
        .time-large { font-size: 1.1rem; font-weight: 700; color: #1e293b; line-height: 1; }
        .time-small { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-top: 2px; }
        body.bg-dark .time-large { color: #f1f5f9; }

        .timeline-marker {
            width: 14px; height: 14px; border-radius: 50%;
            background: #cbd5e1; border: 3px solid #fff; /* Thicker border for separation */
            position: relative; z-index: 1; margin-top: 0.4rem;
            box-shadow: 0 0 0 1px #e2e8f0; /* Ring effect */
        }
        body.bg-dark .timeline-marker { border-color: #1a202c; box-shadow: 0 0 0 1px #4a5568; }
        
        /* Status Colors for Marker */
        .marker-pending { background-color: #D1D100; }
        .marker-approved { background-color: #00D100; }
        .marker-completed { background-color: #009fb1; }
        .marker-cancelled { background-color: #D10000; }
        .marker-rescheduled { background-color: #f59e0b; }

        .timeline-content {
            flex-grow: 1; background: #fff; border: 1px solid #f1f5f9;
            border-radius: 12px; padding: 1rem;
            transition: all 0.2s ease; position: relative;
        }
        .timeline-content:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px); border-color: #e2e8f0;
        }
        body.bg-dark .timeline-content { background: #1e2124; border-color: #2d3748; }
        body.bg-dark .timeline-content:hover { border-color: #4a5568; }

        .avatar-circle {
            width: 42px; height: 42px; border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white; display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 0.9rem; flex-shrink: 0;
        }
        
        /* Calendar Styles (Ported from Appointments) */
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 0.8rem; }
        .calendar-header { text-align: center; font-weight: 600; padding: 0.5rem; background-color: #f8f9fa; border: 1px solid #dee2e6; }
        .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; cursor: pointer; transition: all 0.2s ease; position: relative; }
        .calendar-day:hover { background-color: #e9ecef; }
        .calendar-day.selected { background-color: #009fb1; color: white; border-color: #009fb1; }
        .calendar-day.occupied { background-color: #D10000; color: #fff; border-color: #D10000; }
        .calendar-day.partially-occupied { background-color: #D1D100; color: #fff; border-color: #D1D100; }
        .calendar-day.weekend { background-color: #f8f9fa; color: #6c757d; }
        .calendar-day.past { background-color: #e9ecef; color: #adb5bd; cursor: not-allowed; }
        .calendar-day .day-number { font-weight: 600; font-size: 0.9rem; z-index: 1; position: relative; display: flex; align-items: center; justify-content: center; height: 70%; }
        .calendar-day .slot-indicator { position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%); font-size: 0.55rem; background: rgba(0, 0, 0, 0.15); color: #666; padding: 1px 4px; border-radius: 3px; font-weight: 600; z-index: 2; line-height: 1; width: auto; white-space: nowrap; }
        
        .time-slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 0.5rem; }
        .time-slot { padding: 0.75rem; border: 1px solid #dee2e6; border-radius: 0.375rem; text-align: center; cursor: pointer; transition: all 0.2s ease; }
        .time-slot.available { background-color: #00D100; border-color: #00D100; color: #fff; }
        .time-slot.occupied { background-color: #D10000; border-color: #D10000; color: #fff; cursor: not-allowed; }
        .time-slot.past { background-color: #e9ecef; border-color: #dee2e6; color: #6c757d; cursor: not-allowed; opacity: 0.6; }
        .time-slot.selected { background-color: #009fb1; border-color: #009fb1; color: white; }
        .time-slot .time { font-weight: 600; font-size: 0.9rem; }
        .time-slot .status { font-size: 0.75rem; margin-top: 0.25rem; }

        /* Dark Mode Calendar */
        body.bg-dark .calendar-header { background-color: #2a2f35; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .calendar-day { border-color: #2a2f35; color: #e6e6e6; }
        body.bg-dark .calendar-day:hover { background-color: #2a2f35; }
        body.bg-dark .calendar-day.weekend, body.bg-dark .calendar-day.past { background-color: #1e2124; color: #6c757d; }
        body.bg-dark .time-slot { border-color: #2a2f35; }
        body.bg-dark .time-slot.past { background-color: #1e2124; border-color: #2a2f35; color: #6c757d; opacity: 0.5; }

        /* Skeleton Animation */
        .skeleton { background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%); border-radius: 5px; background-size: 200% 100%; animation: 1.5s shine linear infinite; }
        body.bg-dark .skeleton { background: linear-gradient(110deg, #2a2f35 8%, #32383e 18%, #2a2f35 33%); background-size: 200% 100%; }
        @keyframes shine { to { background-position-x: -200%; } }
        /* Timeline Scrollable Section */
        .timeline-scroll-container {
            max-height: 480px;
            overflow-y: auto;
            padding-right: 1rem;
            margin-right: -0.5rem;
        }
        
        /* Custom Scrollbar */
        .timeline-scroll-container::-webkit-scrollbar { width: 5px; }
        .timeline-scroll-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .timeline-scroll-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .timeline-scroll-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        body.bg-dark .timeline-scroll-container::-webkit-scrollbar-track { background: #1a202c; }
        body.bg-dark .timeline-scroll-container::-webkit-scrollbar-thumb { background: #4a5568; }

        .calendar-skeleton { height: 300px; width: 100%; }
    </style>
@endsection

@section('content')
<div class="analytics-container container-fluid px-0">

    <!-- KPI Row -->
    <div class="kpi-row">
        <!-- Total Patients -->
        <div class="kpi-card">
            <div class="kpi-label fw-bold">Patient Base</div>
            <div class="kpi-value">{{ number_format($totalPatients ?? 0) }}</div>
            <div class="text-muted">
                @if(($newPatientsThisMonth ?? 0) > 0)
                    <span class="text-success"><i class="fas fa-arrow-up"></i> +{{ $newPatientsThisMonth }} New</span> this month
                @else
                    <span class="text-muted">No new patients this month</span>
                @endif
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="kpi-card">
            <div class="kpi-label fw-bold">Today's Schedule</div>
            <div class="kpi-value">{{ number_format($todayAppointments ?? 0) }}</div>
            <div class="text-muted">
                <span class="text-primary">{{ $todayCompleted ?? 0 }} Done</span> • 
                <span class="text-warning">{{ $todayPending ?? 0 }} Pending</span>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="kpi-card" style="{{ ($lowStockItems ?? 0) > 0 ? 'border-left: 4px solid #D1D100;' : '' }}">
            <div class="kpi-label fw-bold">Inventory Alerts</div>
            <div class="kpi-value">{{ number_format($lowStockItems ?? 0) }}</div>
            <div class="text-muted">
                @if(($lowStockItems ?? 0) > 0)
                    <span class="text-warning">Requires Restocking</span>
                @else
                    <span class="text-success">Stock Levels Healthy</span>
                @endif
            </div>
        </div>

        <!-- Monthly Services -->
        <div class="kpi-card">
             <div class="kpi-label fw-bold">Services Provided</div>
             <div class="kpi-value">{{ number_format($monthlyServices ?? 0) }}</div>
             <div class="text-muted">
                <span>This Month</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Dashboard Overview (Line Chart) -->
        <div class="col-lg-8">
            <div class="chart-section">
                <div class="section-header">
                    <div class="header-title">Activity Overview</div>
                    <div class="d-flex" id="overviewFilter">
                        <button class="btn-filter active" onclick="updateOverviewChart('weekly', this)">Week</button>
                        <button class="btn-filter" onclick="updateOverviewChart('monthly', this)">Month</button>
                    </div>
                </div>
                <div style="height: 300px;">
                    <canvas id="overviewChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Patients by Barangay (Doughnut) -->
        <div class="col-lg-4">
            <div class="chart-section d-flex flex-column">
                <div class="section-header">
                    <div class="header-title">Demographics</div>
                    <small class="text-muted">By Barangay</small>
                </div>
                <div class="flex-grow-1 position-relative d-flex justify-content-center align-items-center">
                     <canvas id="barangayChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="row g-4">
        <!-- Services Chart (Bar) -->
        <div class="col-lg-6">
             <div class="chart-section d-flex flex-column h-100">
                <div class="section-header">
                    <div class="header-title">Service Demand</div>
                    <div class="d-flex" id="serviceFilter">
                         <button class="btn-filter" onclick="updateServiceChart('weekly', this)">Week</button>
                         <button class="btn-filter active" onclick="updateServiceChart('monthly', this)">Month</button>
                         <button class="btn-filter" onclick="updateServiceChart('yearly', this)">Year</button>
                    </div>
                </div>
                <div class="flex-grow-1" style="min-height: 250px;">
                    <canvas id="serviceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Today's Schedule (List) -->
        <div class="col-lg-6">
            <div class="chart-section">
                <div class="section-header">
                    <div class="header-title">Today's Schedule</div>
                     <span class="header-title" style="font-size: 0.9rem;">{{ \Carbon\Carbon::today()->format('M d, Y') }}</span>
                </div>
                
                @if(($todaysAppointments ?? collect([]))->count() > 0)
                    <div class="timeline-scroll-container">
                        <div class="timeline-container mt-2">
                            @foreach($todaysAppointments as $appointment)
                                <div class="timeline-item">
                                    <!-- Time Column -->
                                    <div class="timeline-time">
                                        <div class="time-large">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i') }}</div>
                                        <div class="time-small">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('A') }}</div>
                                    </div>

                                    <!-- Connecting Line -->
                                    <div class="timeline-line"></div>
                                    <div class="timeline-marker marker-{{ $appointment->status }}"></div>

                                    <!-- Content Card -->
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <!-- Left: Avatar + Details -->
                                            <div class="d-flex gap-3 align-items-center">
                                                <div class="avatar-circle">
                                                    {{ strtoupper(substr($appointment->patient_name, 0, 1)) }}{{ strtoupper(substr(strstr($appointment->patient_name, ' '), 1, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 1rem;">
                                                        {{ $appointment->patient_name }}
                                                    </h6>
                                                    <div class="text-muted small d-flex align-items-center gap-2">
                                                        <span><i class="fas fa-notes-medical text-primary me-1"></i> {{ $appointment->service_type }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right: Status -->
                                            <div class="text-end">
                                                @php
                                                    $statusConfig = match($appointment->status) {
                                                        'approved' => ['bg-success', 'fa-check-circle', 'Confirmed'],
                                                        'pending' => ['bg-warning text-dark', 'fa-clock', 'Pending'],
                                                        'completed' => ['bg-info text-primary', 'fa-check-double', 'Completed'],
                                                        'cancelled' => ['bg-danger', 'fa-times-circle', 'Cancelled'],
                                                        'rescheduled' => ['bg-warning text-dark', 'fa-redo', 'Rescheduled'],
                                                        default => ['bg-secondary', 'fa-circle', ucfirst($appointment->status)]
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusConfig[0] }} rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm" style="font-weight: 600; font-size: 0.75rem;">
                                                    <i class="fas {{ $statusConfig[1] }}"></i> {{ $statusConfig[2] }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Footer (Lighter Divider + Metadata) -->
                                        <div class="mt-3 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                            
                                            <!-- Metadata (ID / Duration) -->
                                            <div class="d-flex align-items-center gap-3 text-muted small">
                                                <span title="Appointment ID"><i class="fas fa-hashtag me-1 opacity-50"></i>{{ $appointment->id }}</span>
                                                <span class="opacity-50">|</span>
                                                <span><i class="fas fa-hourglass-half me-1 opacity-50"></i>30m</span>
                                            </div>

                                            <!-- Actions -->
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="hover-actions d-flex gap-2 me-2">
                                                    @if($appointment->status === 'approved')
                                                        <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="completed">
                                                            <button type="submit" class="btn btn-sm btn-outline-success btn-icon" title="Mark as Done" style="width: 28px; height: 28px;">
                                                                <i class="fas fa-check" style="font-size: 0.8rem;"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>

                                                @if($appointment->status === 'approved')
                                                    <button class="btn btn-sm btn-outline-warning px-3 rounded-pill fw-bold reschedule-btn" 
                                                            data-appointment-id="{{ $appointment->id }}"
                                                            data-action-url="{{ route('admin.appointment.update', $appointment) }}"
                                                            style="font-size: 0.8rem; border-width: 1.5px;">
                                                        <i class="fas fa-calendar-alt me-1"></i> Resched
                                                    </button>
                                                @endif

                                                <button class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold" 
                                                        data-bs-toggle="modal" data-bs-target="#viewAppointmentModal{{ $appointment->id }}"
                                                        style="font-size: 0.8rem; border-width: 1.5px;">
                                                    View <i class="fas fa-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted py-5 text-center">
                         <div class="bg-light rounded-circle p-4 mb-3">
                             <i class="fas fa-calendar-check text-success" style="font-size: 2.5rem; opacity: 0.8;"></i>
                         </div>
                         <h6 class="fw-bold text-dark mb-1">No appointments scheduled for today</h6>
                         <p class="small mb-0">Enjoy a lighter workload!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Walk-in Modal (Retained Functionality) -->
    <div class="modal fade" id="walkInModal" tabindex="-1">
         <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Add Walk-in Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.walk-in.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-uppercase text-muted fw-bold">Patient Name</label>
                            <input type="text" class="form-control" name="patient_name" required placeholder="Full Name">
                        </div>
                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label class="form-label small text-uppercase text-muted fw-bold">Phone</label>
                                <input type="tel" class="form-control" name="patient_phone" required placeholder="09xxxxxxxxx">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-uppercase text-muted fw-bold">Service</label>
                                <select class="form-select" name="service_type" required>
                                    <option value="" disabled selected>Select...</option>
                                    <option value="General Checkup">General Checkup</option>
                                    <option value="Prenatal">Prenatal</option>
                                    <option value="Medical Check-up">Medical Check-up</option>
                                    <option value="Immunization">Immunization</option>
                                    <option value="Family Planning">Family Planning</option>
                                </select>
                             </div>
                        </div>
                         <div class="mb-3">
                            <label class="form-label small text-uppercase text-muted fw-bold">Address</label>
                            <textarea class="form-control" name="patient_address" rows="2" required></textarea>
                        </div>
                         <div class="mb-3">
                            <label class="form-label small text-uppercase text-muted fw-bold">Notes</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">Register Walk-In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Appointment Modals (Loop) -->
    @if(($todaysAppointments ?? collect([]))->count() > 0)
        @foreach($todaysAppointments as $appointment)
            <div class="modal fade" id="viewAppointmentModal{{ $appointment->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Appointment Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <!-- Left Column: Patient Info -->
                                <div class="col-md-6">
                                    <div class="info-card p-3 rounded-3 h-100">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3 d-flex align-items-center">
                                            <i class="fas fa-user-circle me-2"></i>Patient Information
                                        </h6>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Name</label>
                                            <div class="info-value">{{ $appointment->patient_name }}</div>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Contact</label>
                                            <div class="info-value">
                                                <div><i class="fas fa-phone me-2 text-muted"></i>{{ $appointment->patient_phone }}</div>
                                                <div class="mt-1"><i class="fas fa-envelope me-2 text-muted"></i>{{ $appointment->user->email ?? 'No email linked' }}</div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <label class="info-label">Address</label>
                                            <div class="info-value"><i class="fas fa-map-marker-alt me-2 text-muted"></i>{{ $appointment->patient_address ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Appointment Info -->
                                <div class="col-md-6">
                                    <div class="info-card p-3 rounded-3 h-100">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3 d-flex align-items-center">
                                            <i class="fas fa-calendar-check me-2"></i>Appointment Information
                                        </h6>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Service</label>
                                            <div class="info-value text-primary fw-bold">
                                                <i class="fas fa-stethoscope me-2"></i>{{ $appointment->service_type }}
                                            </div>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Date & Time</label>
                                            <div class="info-value">
                                                <div class="mb-1">
                                                    <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                                    {{ $appointment->appointment_date->format('F d, Y') }}
                                                </div>
                                                <div>
                                                    <i class="fas fa-clock me-2 text-muted"></i>
                                                    {{ $appointment->appointment_time }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Status</label>
                                            @php
                                                $statusDisplay = [
                                                    'pending' => 'Pending',
                                                    'approved' => 'Confirmed',
                                                    'rescheduled' => 'Rescheduled',
                                                    'cancelled' => 'Cancelled',
                                                    'completed' => 'Completed',
                                                    'no_show' => 'No Show'
                                                ][$appointment->status] ?? ucfirst($appointment->status);
                                            @endphp
                                            <div>
                                                <span class="status-badge status-{{ $appointment->status }}">{{ $statusDisplay }}</span>
                                            </div>
                                        </div>
                                        @if($appointment->notes)
                                            <div class="info-item">
                                                <label class="info-label">Notes</label>
                                                <div class="notes-box p-2 rounded">{{ $appointment->notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Reschedule Appointment Modal (Single Instance) -->
    <div class="modal fade" id="rescheduleAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reschedule Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rescheduleForm" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="rescheduled">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label">New Appointment Date & Time <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="reschedPrevMonth">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <h6 class="mb-0" id="reschedCurrentMonth">Loading...</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="reschedNextMonth">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div id="reschedCalendarGrid" class="calendar-grid">
                                        <div class="col-12">
                                            <div class="skeleton calendar-skeleton"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="mb-0">Time Slots</h6>
                                        </div>
                                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                            <div id="reschedSelectedDateDisplay" class="mb-3 text-muted">Select a date to view available time slots</div>
                                            <div id="reschedTimeSlotsGrid" class="time-slots-grid">
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                                    <p>Select a date to view time slots</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Hidden inputs to store selected date and time -->
                            <input type="hidden" id="resched_new_date" name="new_date" required>
                            <input type="hidden" id="resched_new_time" name="new_time" required>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = @json($chartData);
        const barangayData = @json($patientsByBarangay);

        // Detect dark mode
        const isDarkMode = document.body.classList.contains('bg-dark');
        const legendColor = isDarkMode ? '#e2e8f0' : '#334155';
        const gridColor = isDarkMode ? '#2d3748' : '#f1f5f9';
        const tickColor = isDarkMode ? '#94a3b8' : '#64748b';

        // 1. Overview Chart (Line) - Improved Styling
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        // Gradient for Line Chart
        let ovGradient = overviewCtx.createLinearGradient(0, 0, 0, 300);
        ovGradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)'); // Blue tint
        ovGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        let overviewChart = new Chart(overviewCtx, {
            type: 'line',
            data: { 
                labels: [], 
                datasets: [{ 
                    label: 'Appointments', 
                    data: [], 
                    borderColor: '#3b82f6', 
                    backgroundColor: ovGradient, 
                    fill: true, 
                    tension: 0.4, 
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { color: gridColor }, 
                        ticks: { 
                            stepSize: 1,
                            color: tickColor
                        } 
                    }, 
                    x: { 
                        grid: { display: false },
                        ticks: { color: tickColor }
                    } 
                }, 
                plugins: { 
                    legend: { 
                        display: false 
                    }, 
                    tooltip: { 
                        mode: 'index', 
                        intersect: false 
                    } 
                } 
            }
        });

        function updateOverviewChart(timeframe, element) {
            document.querySelectorAll('#overviewFilter .btn-filter').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            
            const raw = chartData.overview[timeframe];
            let labels = [], data = [];
            
            if (timeframe === 'weekly') {
                 const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                 for(let i=1; i<=7; i++) { labels.push(days[i-1]); data.push(raw[i]||0); }
            } else if (timeframe === 'monthly') {
                 const days = new Date(new Date().getFullYear(), new Date().getMonth()+1, 0).getDate();
                 for(let i=1; i<=days; i++) { labels.push(i); data.push(raw[i]||0); }
            } else if (timeframe === 'yearly') {
                 const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                 for(let i=1; i<=12; i++) { labels.push(months[i-1]); data.push(raw[i]||0); }
            }
            
            overviewChart.data.labels = labels; overviewChart.data.datasets[0].data = data; overviewChart.update();
        }

        // 2. Services Chart (Bar) - Vibrant Colors
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        let serviceChart = new Chart(serviceCtx, {
            type: 'bar',
            data: { 
                labels: [], 
                datasets: [{ 
                    label: 'Demand', 
                    data: [], 
                    // Vibrant Palette
                    backgroundColor: [
                        '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'
                    ], 
                    borderRadius: 6,
                    borderWidth: 0,
                    hoverBackgroundColor: [
                        '#4f46e5', '#059669', '#d97706', '#dc2626', '#7c3aed', '#0891b2', '#db2777'
                    ]                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { color: gridColor }, 
                        ticks: { 
                            stepSize: 1,
                            color: tickColor
                        } 
                    }, 
                    x: { 
                        grid: { display: false },
                        ticks: { color: tickColor }
                    } 
                }, 
                plugins: { 
                    legend: { display: false } 
                } 
            }
        });


        function updateServiceChart(timeframe, element) {
            document.querySelectorAll('#serviceFilter .btn-filter').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            
            const raw = chartData.services[timeframe];
            const labels = raw.map(i => i.service_type);
            const data = raw.map(i => i.count);
            
            serviceChart.data.labels = labels; 
            serviceChart.data.datasets[0].data = data; 
            serviceChart.update();
        }

        // 3. Barangay (Doughnut)
        const barangayCtx = document.getElementById('barangayChart').getContext('2d');
        new Chart(barangayCtx, {
            type: 'doughnut',
            data: {
                labels: barangayData.map(i => i.barangay),
                datasets: [{
                    data: barangayData.map(i => i.count),
                    backgroundColor: ['#009fb1', '#00D100', '#D1D100', '#D10000', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { 
                        position: 'right', 
                        labels: { 
                            boxWidth: 10,
                            color: legendColor
                        } 
                    } 
                } 
            }
        });

        // AppointmentCalendar class and Reschedule logic
        class AppointmentCalendar {
            constructor(config) {
                this.config = config;
                this.currentDate = new Date();
                this.selectedDate = null;
                this.calendarData = [];
                this.init();
            }
            init() { this.attachEventListeners(); this.loadCalendar(); }
            attachEventListeners() {
                const prevBtn = document.getElementById(this.config.prevBtnId);
                const nextBtn = document.getElementById(this.config.nextBtnId);
                if (prevBtn) prevBtn.addEventListener('click', () => { this.currentDate.setMonth(this.currentDate.getMonth() - 1); this.loadCalendar(); });
                if (nextBtn) nextBtn.addEventListener('click', () => { this.currentDate.setMonth(this.currentDate.getMonth() + 1); this.loadCalendar(); });
            }
            async loadCalendar() {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth() + 1;
                try {
                    const response = await fetch(`/admin/appointments/calendar?year=${year}&month=${month}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();
                    this.calendarData = data.calendar;
                    this.renderCalendar();
                    this.updateMonthDisplay();
                } catch (error) { console.error('Error loading calendar:', error); }
            }
            renderCalendar() {
                const calendarGrid = document.getElementById(this.config.calendarGridId);
                if (!calendarGrid) return;
                calendarGrid.innerHTML = '';
                ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
                    const header = document.createElement('div'); header.className = 'calendar-header'; header.textContent = day; calendarGrid.appendChild(header);
                });
                const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                for (let i = 0; i < firstDay; i++) calendarGrid.appendChild(document.createElement('div'));
                this.calendarData.forEach(dayData => {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    dayElement.dataset.date = dayData.date;
                    const dayNumber = document.createElement('span'); dayNumber.className = 'day-number'; dayNumber.textContent = dayData.day; dayElement.appendChild(dayNumber);
                    if (dayData.is_weekend || dayData.is_past) {
                        dayElement.classList.add(dayData.is_weekend ? 'weekend' : 'past');
                        dayElement.style.opacity = '0.5'; dayElement.style.cursor = 'not-allowed'; dayElement.style.pointerEvents = 'none';
                    } else {
                        if (dayData.is_fully_occupied) dayElement.classList.add('occupied');
                        else if (dayData.occupied_slots > 0) dayElement.classList.add('partially-occupied');
                        dayElement.addEventListener('click', () => this.selectDate(dayData.date));
                    }
                    if (dayData.occupied_slots > 0) {
                        const indicator = document.createElement('span'); indicator.className = 'slot-indicator'; indicator.textContent = `${dayData.occupied_slots}/${dayData.total_slots}`; dayElement.appendChild(indicator);
                    }
                    calendarGrid.appendChild(dayElement);
                });
            }
            updateMonthDisplay() {
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const displayEl = document.getElementById(this.config.currentMonthId);
                if (displayEl) displayEl.textContent = `${monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`;
            }
            async selectDate(date) {
                const calendarGrid = document.getElementById(this.config.calendarGridId);
                if (calendarGrid) {
                    calendarGrid.querySelectorAll('.calendar-day.selected').forEach(el => el.classList.remove('selected'));
                    calendarGrid.querySelectorAll('.calendar-day').forEach(el => { if (el.dataset.date === date) el.classList.add('selected'); });
                }
                this.selectedDate = date;
                const formattedDate = new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                const dateDisplay = document.getElementById(this.config.selectedDateDisplayId);
                if (dateDisplay) dateDisplay.textContent = formattedDate;
                await this.loadTimeSlots(date);
            }
            async loadTimeSlots(date) {
                try {
                    const response = await fetch(`/admin/appointments/slots?date=${date}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();
                    this.renderTimeSlots(data.slots);
                } catch (error) { console.error('Error loading time slots:', error); }
            }
            renderTimeSlots(slots) {
                const timeSlotsGrid = document.getElementById(this.config.timeSlotsGridId);
                if (!timeSlotsGrid) return;
                timeSlotsGrid.innerHTML = '';
                if (!slots || slots.length === 0) { timeSlotsGrid.innerHTML = '<div class="text-center text-muted">No time slots available</div>'; return; }
                slots.forEach(slot => {
                    const slotElement = document.createElement('div');
                    let slotClass = 'time-slot'; let statusText = '';
                    if (slot.is_past) { slotClass += ' past'; statusText = 'Unavailable'; }
                    else if (slot.available) { slotClass += ' available'; statusText = 'Available'; }
                    else { slotClass += ' occupied'; statusText = `Occupied (${slot.occupied_count})`; }
                    slotElement.className = slotClass;
                    if (slot.available && !slot.is_past) slotElement.addEventListener('click', () => this.selectTimeSlot(slot.time, slot.display));
                    const timeElement = document.createElement('div'); timeElement.className = 'time'; timeElement.textContent = slot.display;
                    const statusElement = document.createElement('div'); statusElement.className = 'status'; statusElement.textContent = statusText;
                    slotElement.appendChild(timeElement); slotElement.appendChild(statusElement);
                    timeSlotsGrid.appendChild(slotElement);
                });
            }
            selectTimeSlot(time, display) {
                const timeSlotsGrid = document.getElementById(this.config.timeSlotsGridId);
                if (timeSlotsGrid) {
                    timeSlotsGrid.querySelectorAll('.time-slot.selected').forEach(el => el.classList.remove('selected'));
                    timeSlotsGrid.querySelectorAll('.time-slot').forEach(el => {
                        const timeEl = el.querySelector('.time');
                        if (timeEl && timeEl.textContent === display) el.classList.add('selected');
                    });
                }
                this.selectedTime = time;
                const timeInput = document.getElementById(this.config.timeInputId);
                const dateInput = document.getElementById(this.config.dateInputId);
                if (timeInput) timeInput.value = time;
                if (dateInput) dateInput.value = this.selectedDate;
            }
        }

        const rescheduleModal = document.getElementById('rescheduleAppointmentModal');
        let rescheduleCalendar = null;
        if (rescheduleModal) {
            rescheduleModal.addEventListener('shown.bs.modal', function () {
                if (!rescheduleCalendar) {
                    rescheduleCalendar = new AppointmentCalendar({
                        prevBtnId: 'reschedPrevMonth', nextBtnId: 'reschedNextMonth', currentMonthId: 'reschedCurrentMonth',
                        calendarGridId: 'reschedCalendarGrid', selectedDateDisplayId: 'reschedSelectedDateDisplay',
                        timeSlotsGridId: 'reschedTimeSlotsGrid', dateInputId: 'resched_new_date', timeInputId: 'resched_new_time'
                    });
                }
            });
            rescheduleModal.addEventListener('hidden.bs.modal', function () {
                if (rescheduleCalendar) {
                    rescheduleCalendar.selectedDate = null; rescheduleCalendar.selectedTime = null;
                    document.getElementById('reschedSelectedDateDisplay').textContent = 'Select a date to view available time slots';
                    document.getElementById('reschedTimeSlotsGrid').innerHTML = '<div class="text-center text-muted"><i class="fas fa-clock fa-2x mb-2"></i><p>Select a date to view time slots</p></div>';
                    document.getElementById('resched_new_date').value = ''; document.getElementById('resched_new_time').value = '';
                    document.querySelectorAll('#reschedCalendarGrid .selected').forEach(el => el.classList.remove('selected'));
                }
            });
        }
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.reschedule-btn');
            if (btn) {
                document.getElementById('rescheduleForm').action = btn.dataset.actionUrl;
                new bootstrap.Modal(rescheduleModal).show();
            }
        });

        // Init Charts
        updateOverviewChart('weekly', document.querySelectorAll('#overviewFilter button')[0]);
        updateServiceChart('monthly', document.querySelectorAll('#serviceFilter button')[0]);
    </script>
@endpush