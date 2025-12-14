<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patient Reports</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 5px; font-size: 18px; }
        h2 { color: #2c3e50; font-size: 14px; border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 20px; margin-bottom: 10px; }
        .header { text-align: center; margin-bottom: 30px; }
        .date { text-align: center; color: #7f8c8d; font-size: 12px; margin-bottom: 20px; }
        
        /* Summary Cards */
        .summary-grid { display: block; margin-bottom: 20px; width: 100%; text-align: center; }
        .summary-card { display: inline-block; width: 22%; background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; margin: 0 1%; border-radius: 5px; vertical-align: top; }
        .summary-value { font-size: 18px; font-weight: bold; color: #2c3e50; margin-top: 5px; }
        .summary-label { font-size: 10px; color: #7f8c8d; text-transform: uppercase; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f8f9fa; padding: 6px; text-align: left; border: 1px solid #dee2e6; font-weight: bold; font-size: 10px; color: #444; }
        td { padding: 6px; border: 1px solid #dee2e6; font-size: 10px; }
        tr:nth-child(even) { background-color: #fbfbfb; }
        
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        
        /* Layout */
        .row { width: 100%; display: block; clear: both; }
        .col-half { width: 48%; display: inline-block; vertical-align: top; margin-right: 1%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Patient Demographics & Analytics Report</h1>
        <div class="date">
            Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Patients</div>
            <div class="summary-value">{{ number_format($totalPatients) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Male</div>
            <div class="summary-value">{{ number_format($maleCount) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Female</div>
            <div class="summary-value">{{ number_format($femaleCount) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">New (Period)</div>
            <div class="summary-value">{{ number_format($newPatients) }}</div>
        </div>
    </div>

    <!-- Distributions -->
    <div class="row">
        <div class="col-half">
            <h2>Age Distribution</h2>
            <table>
                <thead>
                    <tr>
                        <th>Age Group</th>
                        <th class="text-end">Count</th>
                        <th class="text-end">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ageGroups as $group => $count)
                    <tr>
                        <td>{{ $group }}</td>
                        <td class="text-end">{{ $count }}</td>
                        <td class="text-end">{{ $totalPatients > 0 ? round(($count / $totalPatients) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-half" style="margin-right: 0; margin-left: 1%;">
            <h2>Barangay Distribution</h2>
            <table>
                <thead>
                    <tr>
                        <th>Barangay</th>
                        <th class="text-end">Count</th>
                        <th class="text-end">%</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangayDistribution as $item)
                    <tr>
                        <td>{{ $item->barangay }}</td>
                        <td class="text-end">{{ $item->count }}</td>
                        <td class="text-end">{{ $totalPatients > 0 ? round(($item->count / $totalPatients) * 100, 1) : 0 }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Patients -->
    <h2>Top Patients (Appointments in Period)</h2>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th class="text-end">Appointments</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topPatients as $patient)
            <tr>
                <td>{{ $patient->name }}</td>
                <td>{{ $patient->email }}</td>
                <td>{{ $patient->phone }}</td>
                <td class="text-end">{{ $patient->appointments_count }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">No appointments found in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Recent Registrations -->
    <h2>New Registrations (In Period)</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Barangay</th>
                <th class="text-end">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentPatients as $patient)
            <tr>
                <td>{{ $patient->name }}</td>
                <td>{{ ucfirst($patient->gender) }}</td>
                <td>{{ $patient->age }}</td>
                <td>{{ $patient->barangay === 'Other' ? $patient->barangay_other : $patient->barangay }}</td>
                <td class="text-end">{{ optional($patient->created_at)->format('M d, Y h:i A') ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No new registrations in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: center; color: #999; font-size: 9px; border-top: 1px solid #eee; padding-top: 10px;">
        Generated on {{ now()->format('F d, Y h:i A') }}
    </div>
</body>
</html>
