@extends('admin.layout')

@section('title', 'MALASAKIT - Admin Dashboard')
@section('page-title', 'Dashboard Overview')
@section('page-description', "Welcome back! Here's what's happening today.")

@section('page-styles')
    <style>
        /* Shared Analytics Theme Repetition */
        .analytics-container { max-width: 1400px; }

        /* KPI Cards */
        .kpi-row { display: flex; gap: 1rem; margin-bottom: 2rem; overflow-x: auto; padding-bottom: 0.5rem; }
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
        
        .text-trend-up { color: #10b981; }
        .text-trend-down { color: #ef4444; }
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
    </style>
@endsection

@section('content')
<div class="analytics-container container-fluid px-2">

    <!-- KPI Row -->
    <div class="kpi-row">
        <!-- Total Patients -->
        <div class="kpi-card">
            <div class="kpi-label">Patient Base</div>
            <div class="kpi-value">{{ number_format($totalPatients ?? 0) }}</div>
            <div class="kpi-sub">
                @if(($patientsChange ?? 0) > 0)
                    <i class="fas fa-arrow-up text-trend-up"></i> <span class="text-trend-up">{{ abs($patientsChange) }}%</span> vs last month
                @elseif(($patientsChange ?? 0) < 0)
                    <i class="fas fa-arrow-down text-trend-down"></i> <span class="text-trend-down">{{ abs($patientsChange) }}%</span> vs last month
                @else
                    <span class="text-neutral">No change</span>
                @endif
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="kpi-card">
            <div class="kpi-label">Today's Schedule</div>
            <div class="kpi-value">{{ number_format($todayAppointments ?? 0) }}</div>
            <div class="kpi-sub">
                <span class="text-primary fw-bold">{{ $todayCompleted ?? 0 }} Done</span> • 
                <span class="text-warning fw-bold">{{ $todayPending ?? 0 }} Pending</span>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="kpi-card" style="{{ ($lowStockItems ?? 0) > 0 ? 'border-left: 4px solid #f59e0b;' : '' }}">
            <div class="kpi-label">Inventory Alerts</div>
            <div class="kpi-value">{{ number_format($lowStockItems ?? 0) }}</div>
            <div class="kpi-sub">
                @if(($lowStockItems ?? 0) > 0)
                    <span class="text-warning">Requires Restocking</span>
                @else
                    <span class="text-success">Stock Levels Healthy</span>
                @endif
            </div>
        </div>

        <!-- Monthly Services -->
        <div class="kpi-card">
             <div class="kpi-label">Services Provided</div>
             <div class="kpi-value">{{ number_format($monthlyServices ?? 0) }}</div>
             <div class="kpi-sub">
                <span class="text-muted">This Month</span>
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
                     <span class="badge bg-light text-dark">{{ \Carbon\Carbon::today()->format('M d, Y') }}</span>
                </div>
                
                @if(($todaysAppointments ?? collect([]))->count() > 0)
                    <div class="d-flex flex-column">
                        @foreach($todaysAppointments as $appointment)
                            <div class="activity-item">
                                <div class="activity-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">
                                        {{ $appointment->patient_name ?? ($appointment->user->name ?? 'Walk-in Patient') }}
                                    </h6>
                                    <small class="text-muted">{{ $appointment->service_type }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</div>
                                    <span class="badge bg-light text-secondary border">{{ ucfirst($appointment->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted" style="min-height: 200px;">
                        <i class="fas fa-calendar-day mb-3" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p>No appointments for today.</p>
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

</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = @json($chartData);
        const barangayData = @json($patientsByBarangay);

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
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } }, 
                    x: { grid: { display: false } } 
                }, 
                plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } } 
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
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'
                    ], 
                    borderRadius: 4 
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                scales: { 
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } }, 
                    x: { grid: { display: false } } 
                }, 
                plugins: { legend: { display: false } } 
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
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 10 } } } }
        });

        // Init
        updateOverviewChart('weekly', document.querySelectorAll('#overviewFilter button')[0]);
        updateServiceChart('monthly', document.querySelectorAll('#serviceFilter button')[0]);
    </script>
@endpush