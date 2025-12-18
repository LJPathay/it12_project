@extends('admin.layout')

@section('title', 'Malasakit | Analytics')
@section('page-title', 'Clinic Analytics')
@section('page-description', 'Performance metrics, efficiency analysis, and long-term trends.')

@section('page-styles')
    <style>
        /* Modern Analytics Theme */
        .analytics-container { max-width: 1400px; }
        
        /* KPI Cards - Compact & Data-First */
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
        .kpi-sub { font-size: 0.8rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.25rem; }
        .text-trend-up { color: #00D100; }
        .text-trend-down { color: #D10000; }

        /* Chart Cards */
        .chart-section {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; height: 100%;
        }
        .section-header { margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .header-title { font-size: 1.1rem; font-weight: 700; color: #334155; }
        
        /* Data Tables */
        .analytics-table th { 
            background: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; padding: 1rem; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;
        }
        .analytics-table td { padding: 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 0.9rem; }
        .analytics-table tr:hover { background-color: #f8fafc; }
        
        .progress-thin { height: 6px; border-radius: 3px; background-color: #e2e8f0; width: 100px; display: inline-block; }
        .progress-bar-custom { height: 100%; border-radius: 3px; }

        /* Dark Mode */
        body.bg-dark .kpi-card, body.bg-dark .chart-section { background: #1e2124; border-color: #2d3748; }
        body.bg-dark .kpi-value, body.bg-dark .header-title { color: #f1f5f9; }
        body.bg-dark .kpi-label { color: #94a3b8; }
        body.bg-dark .analytics-table th { color: #94a3b8; background: #2d3136; border-color: #2d3748; }
        body.bg-dark .analytics-table td { color: #e2e8f0; border-color: #2d3748; }
        body.bg-dark .analytics-table tr:hover { background-color: #2d3136; }

        /* Filter buttons */
        .btn-filter {
            border: none; background: transparent; color: #94a3b8; font-weight: 600; font-size: 0.85rem; padding: 0.25rem 0.75rem; transition: all 0.2s;
        }
        .btn-filter:hover { color: #64748b; }
        .btn-filter.active { color: #3b82f6; text-decoration: underline; text-underline-offset: 4px; }
        body.bg-dark .btn-filter.active { color: #3b82f6; }
    </style>
@endsection

@section('content')
<div class="analytics-container container-fluid px-2">

    <!-- KPI Row: Performance at a Glance -->
    <div class="kpi-row">
        <!-- Total Appointments -->
        <div class="kpi-card">
            <div class="kpi-label fw-bold">Volume</div>
            <div class="kpi-value">{{ number_format($appointmentStats['total']) }}</div>
            <div class="text-muted">Total Appointments</div>
        </div>
        <!-- Completion Rate -->
        @php 
            $completionRate = $appointmentStats['total'] > 0 ? ($appointmentStats['completed'] / $appointmentStats['total']) * 100 : 0; 
        @endphp
        <div class="kpi-card">
            <div class="kpi-label fw-bold">Success Rate</div>
            <div class="kpi-value text-primary">{{ number_format($completionRate, 1) }}%</div>
            <div class="text-muted">Appointments Completed</div>
        </div>
        <!-- Pending Queue -->
        <div class="kpi-card">
            <div class="kpi-label fw-bold">Backlog</div>
            <div class="kpi-value text-warning">{{ number_format($appointmentStats['pending']) }}</div>
            <div class="text-muted">Pending Processing</div>
        </div>
        <!-- Inventory Value (Proxy: Total Items) -->
        <div class="kpi-card">
            <div class="kpi-label fw-bold">Inventory Health</div>
            <div class="kpi-value">{{ number_format($inventoryStats['total_items']) }}</div>
            <div class="text-muted">
                @if($inventoryStats['low_stock'] > 0)
                    <span class="text-danger">{{ $inventoryStats['low_stock'] }} Alerts</span>
                @else
                    <span class="text-success">Healthy</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Row: Trends & Distribution -->
    <div class="row g-4 mb-4">
        <!-- Main Trend Chart (Comparison) -->
        <div class="col-lg-8">
            <div class="chart-section">
                <div class="section-header">
                    <div class="header-title">Appointment Performance Trends</div>
                    <div class="d-flex align-items-center gap-3">
                        <small id="trendDescription" class="text-muted d-none d-md-inline">Monthly volume by status (This Year)</small>
                        <div class="btn-group btn-group-sm" id="trendFilter">
                            <button type="button" class="btn btn-filter" onclick="updateTrendChart('weekly', this)">Weekly</button>
                            <button type="button" class="btn btn-filter" onclick="updateTrendChart('monthly', this)">Monthly</button>
                            <button type="button" class="btn btn-filter active" onclick="updateTrendChart('yearly', this)">Yearly</button>
                        </div>
                    </div>
                </div>
                <div style="height: 300px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Service Distribution -->
        <div class="col-lg-4">
            <div class="chart-section d-flex flex-column">
                 <div class="section-header">
                    <div class="header-title">Demand by Service</div>
                </div>
                <div class="flex-grow-1 position-relative d-flex justify-content-center align-items-center">
                     <canvas id="serviceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row: Deep Dive -->
    <div class="row g-4">
        <!-- Service Efficiency Report -->
        <div class="col-lg-8">
            <div class="chart-section p-0 overflow-hidden">
                <div class="p-4 border-bottom">
                    <div class="header-title">Service Efficiency Report</div>
                </div>
                <div class="table-responsive">
                    <table class="table analytics-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Service Name</th>
                                <th>Total Demand</th>
                                <th>Completion Rate</th>
                                <th>Cancellation</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servicePerformance as $perf)
                                @php 
                                    $rate = $perf->total > 0 ? ($perf->completed / $perf->total) * 100 : 0;
                                    $cancelRate = $perf->total > 0 ? ($perf->cancelled / $perf->total) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $perf->service_type }}</td>
                                    <td>{{ $perf->total }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-thin">
                                                <div class="progress-bar-custom bg-success" style="width: {{ $rate }}%"></div>
                                            </div>
                                            <span class="small fw-bold">{{ number_format($rate, 0) }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ number_format($cancelRate, 1) }}%</td>
                                    <td class="text-center pe-4">
                                        @if($rate >= 80) <span class="badge bg-success bg-opacity-10 text-success">High Perf</span>
                                        @elseif($rate >= 50) <span class="badge bg-warning bg-opacity-10 text-warning">Medium</span>
                                        @else <span class="badge bg-danger bg-opacity-10 text-danger">Needs Attention</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Inventory Summary -->
        <div class="col-lg-4">
             <div class="chart-section p-0 overflow-hidden">
                 <div class="p-4 border-bottom">
                    <div class="header-title">Inventory Composition</div>
                </div>
                <div class="table-responsive">
                    <table class="table analytics-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Category</th>
                                <th class="text-end pe-4">Items Traced</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryByCategory as $cat)
                                <tr>
                                    <td class="ps-4">{{ $cat->category ?: 'Uncategorized' }}</td>
                                    <td class="text-end pe-4 fw-bold">{{ $cat->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
             </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data Injection
        const performanceTrends = @json($performanceTrends);
        const serviceData = @json($serviceTypes);

        // Detect dark mode
        const isDarkMode = document.body.classList.contains('bg-dark');
        const legendColor = isDarkMode ? '#e2e8f0' : '#334155';
        const gridColor = isDarkMode ? '#2d3748' : '#f1f5f9';
        const tickColor = isDarkMode ? '#94a3b8' : '#64748b';

        // 1. Line Chart: Comparison
        const trendCtx = document.getElementById('trendChart')?.getContext('2d');
        let trendChart;
        
        if (trendCtx) {
            trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Completed',
                            data: [],
                            borderColor: '#00D100',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            pointRadius: 4,
                        },
                        {
                            label: 'Pending',
                            data: [],
                            borderColor: '#D1D100',
                            backgroundColor: 'transparent',
                            borderDash: [5, 5],
                            tension: 0.4,
                            pointRadius: 0
                        },
                        {
                            label: 'Cancelled',
                            data: [],
                            borderColor: '#D10000',
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'top', 
                            align: 'end', 
                            labels: { 
                                usePointStyle: true, 
                                boxWidth: 8,
                                color: legendColor
                            } 
                        },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { 
                                precision: 0,
                                color: tickColor
                            }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: tickColor }
                        }
                    }
                }
            });

            // Initialize with default (Yearly)
            updateTrendChart('yearly', document.querySelector('#trendFilter .btn-filter.active'));
        }

        function updateTrendChart(timeframe, element) {
            if (!trendChart) return;

            document.querySelectorAll('#trendFilter .btn-filter').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            
            const raw = performanceTrends[timeframe];
            const description = {
                'weekly': 'Daily volume by status (This Week)',
                'monthly': 'Daily volume by status (This Month)',
                'yearly': 'Monthly volume by status (This Year)'
            };
            document.getElementById('trendDescription').innerText = description[timeframe];

            let labels = [];
            if (timeframe === 'weekly') {
                labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            } else if (timeframe === 'monthly') {
                const days = raw.completed.length;
                for (let i = 1; i <= days; i++) labels.push(i);
            } else if (timeframe === 'yearly') {
                labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            }

            trendChart.data.labels = labels;
            trendChart.data.datasets[0].data = raw.completed;
            trendChart.data.datasets[1].data = raw.pending;
            trendChart.data.datasets[2].data = raw.cancelled;
            trendChart.update();
        }

        // 2. Service Demand (Doughnut)
        const serviceCtx = document.getElementById('serviceChart')?.getContext('2d');
        if (serviceCtx && serviceData.length > 0) {
             const labels = serviceData.map(s => s.service_type);
             const data = serviceData.map(s => s.count);
             
             new Chart(serviceCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#009fb1', '#8b5cf6', '#00D100', '#D1D100', '#64748b'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'right', 
                            labels: { 
                                font: { size: 10 }, 
                                boxWidth: 10,
                                color: legendColor
                            } 
                        }
                    }
                }
             });
        }

    </script>
@endpush