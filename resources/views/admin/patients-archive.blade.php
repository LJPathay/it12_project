@extends('admin.layout')

@section('title', 'Archived Patients - Barangay Health Center')
@section('page-title', 'Archived Patients')
@section('page-description', 'View and manage archived patient accounts')

@section('page-styles')
    <style>
        /* Match Patient Management dark-mode surfaces */
        body.bg-dark .main-content { background-color: #151718; }
        body.bg-dark .sidebar { background: #131516; border-right-color: #2a2f35; }
        body.bg-dark .header { background: #1b1e20; border-bottom-color: #2a2f35; }
        
        /* Dark mode for cards */
        body.bg-dark .card {
            background: #1e2124;
            color: #e6e6e6;
            border-color: #2a2f35;
        }
        
        /* Dark mode for tables */
        body.bg-dark .table {
            color: #d6d6d6;
        }
        
        body.bg-dark .table thead,
        body.bg-dark .table-light {
            background: #1a1f24 !important;
            color: #e6e6e6;
        }
        
        body.bg-dark .table tbody td {
            border-color: #2a2f35;
        }
        
        body.bg-dark .table-hover tbody tr:hover {
            background-color: #2a2f35;
        }
        
        /* Dark mode for text */
        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }
        
        /* Hide Bootstrap pagination's built-in "Showing" text on the left */
        nav[role="navigation"] p,
        nav[role="navigation"] .text-sm {
            display: none !important;
        }
        
        /* Bring showing text closer to pagination */
        #patientsPaginationContainer > div:last-child {
            margin-top: -0.5rem !important;
        }
</style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.patients') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i>
                <span>Back to Patients</span>
            </a>
        </div>
        <div class="text-muted small">
            Archived patients cannot log in until they are restored.
        </div>
    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            @if($patients->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Patient</th>
                                                <th>Email</th>
                                                <th>Archived At</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($patients as $patient)
                                            <tr>
                                                <td>{{ $patient->name }}</td>
                                                <td>{{ $patient->email }}</td>
                                                <td>{{ optional($patient->deleted_at)->format('M d, Y g:i A') }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-success btn-sm d-inline-flex align-items-center mx-auto" data-bs-toggle="modal" data-bs-target="#restorePatientModal{{ $patient->id }}">
                                                        <i class="fas fa-undo me-1"></i>
                                                        <span>Restore</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No archived patients</h5>
                                    <p class="text-muted">Archived patients will appear here when you archive them from the Patient Management page.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($patients->count() > 0)
                        <div class="d-flex flex-column align-items-center mt-4"
                            id="patientsPaginationContainer">
                            <div>
                                {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
                            </div>
                            <div class="small text-muted mb-0 mt-n2">
                                Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of {{ $patients->total() }} archived patients
                            </div>
                        </div>
                    @endif
@endsection

@foreach($patients as $patient)
    <!-- Restore Patient Modal -->
    <div class="modal fade" id="restorePatientModal{{ $patient->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restore Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to restore this patient?</p>
                    <p class="fw-bold mb-0">{{ $patient->name }}</p>
                    <small class="text-muted">{{ $patient->email }}</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.patient.restore', $patient->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Restore</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach