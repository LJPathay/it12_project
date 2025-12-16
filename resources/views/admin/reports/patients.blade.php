@extends('admin.layout')

@section('title', 'Patient Reports - Barangay Health Center')
@section('page-title', 'Patient Reports')
@section('page-description', 'Comprehensive patient analytics and statistics')

@section('content')
    <div class="p-0 p-md-4">
        <!-- Export Buttons -->
        <!-- Export Form -->
        <div class="card-surface p-3 mb-4">
            <form action="{{ route('admin.reports.patients') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required
                        value="{{ request('start_date', $filterStartDate) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control" required
                        value="{{ request('end_date', $filterEndDate) }}">
                </div>
                <!-- Barangay Filter -->
                <div class="col-md-3">
                    <label class="form-label small text-muted">Barangay</label>
                    <select class="form-select" name="barangay">
                        <option value="">All Barangays</option>
                        <option value="Barangay 11" {{ request('barangay') == 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                        <option value="Barangay 12" {{ request('barangay') == 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                        <option value="Other" {{ request('barangay') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div> <!-- End Barangay Filter -->

                <div class="col-md-3">
                     <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Apply Filter
                    </button>
                </div>

                <div class="col-md-12 border-top pt-3 mt-3">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" formaction="{{ route('admin.reports.export.patients') }}"
                            class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Export Excel
                        </button>
                        <button type="submit" formaction="{{ route('admin.reports.export.patients.pdf') }}"
                            class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <p class="text-muted small mb-3">
            Showing results from <strong>{{ \Carbon\Carbon::parse($filterStartDate)->format('M d, Y') }}</strong>
            to <strong>{{ \Carbon\Carbon::parse($filterEndDate)->format('M d, Y') }}</strong>
            @if(request('barangay'))
                for <strong>{{ request('barangay') }}</strong>
            @endif
        </p>

        <!-- Overview Cards -->
         <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card-surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Total Patients</small>
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($totalPatients) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Male Patients
                            ({{ $totalPatients > 0 ? round(($maleCount / $totalPatients) * 100, 1) : 0 }}%)</small>
                        <i class="fas fa-male text-info"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($maleCount) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Female Patients
                            ({{ $totalPatients > 0 ? round(($femaleCount / $totalPatients) * 100, 1) : 0 }}%)</small>
                        <i class="fas fa-female text-danger"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($femaleCount) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">New Patients (Range)</small>
                        <i class="fas fa-user-plus text-success"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($newPatientsInRange) }}</h3>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Age Distribution -->
            <div class="col-md-6">
                <div class="card-surface p-3 h-100">
                    <h5 class="mb-3">Age Distribution</h5>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Age Group</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ageGroups as $group => $count)
                                    <tr>
                                        <td>{{ $group }} years</td>
                                        <td class="text-end"><strong>{{ $count }}</strong></td>
                                        <td class="text-end">
                                            {{ $totalPatients > 0 ? round(($count / $totalPatients) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Barangay Distribution -->
            <div class="col-md-6">
                <div class="card-surface p-3 h-100">
                    <h5 class="mb-3">Barangay Distribution</h5>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangayDistribution as $item)
                                    <tr>
                                        <td>{{ $item->barangay }}</td>
                                        <td class="text-end"><strong>{{ $item->count }}</strong></td>
                                        <td class="text-end">
                                            {{ $totalPatients > 0 ? round(($item->count / $totalPatients) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Patient List (Replaces Top Patients) -->
        <div class="row g-3 mb-4">
             <div class="col-12">
                <div class="card-surface p-3 h-100">
                    <h5 class="mb-3">
                        Patient List 
                        @if(request('barangay'))
                            <span class="badge bg-primary ms-2">{{ request('barangay') }}</span>
                        @endif
                    </h5>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Age/Gender</th>
                                    <th>Barangay</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patients as $patient)
                                    <tr>
                                        <td>{{ $patient->name }}</td>
                                        <td>{{ $patient->age }} / {{ ucfirst($patient->gender) }}</td>
                                        <td>{{ $patient->barangay === 'Other' ? $patient->barangay_other : $patient->barangay }}</td>
                                        <td>{{ $patient->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No patients found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Registrations (Keep or Move?) - Moving to bottom or removing if redundant with list? 
             Let's keep it as a widget or remove since we detailed list. 
             If list has all patients in range "new patients in range", then Recent Registrations is redundant subset.
             But Recent Registrations usually shows globally recent?
             The query for recentPatients IS range bounded. So it IS a subset of $patients.
             I will remove it to de-clutter since we have the full list now.
        -->
    </div>
@endsection