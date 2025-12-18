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
        
        /* Action Buttons Hover Info */
        .hover-actions { opacity: 0; transition: opacity 0.2s ease; }
        .timeline-content:hover .hover-actions { opacity: 1; }

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
             <div class="chart-section">
                <div class="section-header">
                    <div class="header-title">Service Demand</div>
                    <div class="d-flex" id="serviceFilter">
                         <button class="btn-filter" onclick="updateServiceChart('weekly', this)">Week</button>
                         <button class="btn-filter active" onclick="updateServiceChart('monthly', this)">Month</button>
                         <button class="btn-filter" onclick="updateServiceChart('yearly', this)">Year</button>
                    </div>
                </div>
                <div style="height: 250px;">
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
                                                    'completed' => ['bg-info text-dark', 'fa-check-double', 'Completed'],
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
                        '#009fb1', '#00D100', '#D1D100', '#D10000', '#8b5cf6', '#06b6d4', '#ec4899'
                    ], 
                    borderRadius: 4 
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
            
            serviceChart.data.labels = labels; serviceChart.data.datasets[0].data = data; serviceChart.update();
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

        // Init
        updateOverviewChart('weekly', document.querySelectorAll('#overviewFilter button')[0]);
        updateServiceChart('monthly', document.querySelectorAll('#serviceFilter button')[0]);
    </script>
@endpush