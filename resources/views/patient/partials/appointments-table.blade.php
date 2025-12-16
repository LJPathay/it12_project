<!-- Appointments Table -->
<div class="row">
    <div class="col-12">
        <div class="card appointments-card">
            <div class="appointments-header">
                <h5 class="mb-0">All Appointments</h5>
            </div>
            <div class="appointments-body">
                @if($appointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Appointment</th>
                                    <th>Date & Time</th>
                                    <th>Service</th>
                                    <th class="text-center">Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $appointment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="appointment-avatar me-3">
                                                    <i class="fas fa-calendar"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">Appointment #{{ $appointment->id }}</div>
                                                    <small class="text-muted">{{ $appointment->patient_name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    {{ $appointment->appointment_date->format('M d, Y') }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</small>
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $appointment->service_type }}</td>
                                        <td class="text-center">
                                            <span class="status-badge 
                                                                    @if($appointment->status == 'pending') bg-warning
                                                                    @elseif($appointment->status == 'approved') bg-success
                                                                    @elseif($appointment->status == 'completed') bg-info
                                                                    @elseif($appointment->status == 'cancelled') bg-danger
                                                                    @else bg-secondary
                                                                    @endif"
                                                @if($appointment->status == 'cancelled')
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top" 
                                                    title="{{ $appointment->notes ?? 'Appointment cancelled.' }}"
                                                    style="cursor: help;"
                                                @endif
                                            >
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('patient.appointment.show', $appointment) }}"
                                                    class="btn btn-sm btn-primary me-2">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                @if($appointment->status == 'cancelled')
                                                    <a href="{{ route('patient.book-appointment') }}" 
                                                       class="btn btn-sm btn-outline-secondary me-2">
                                                        <i class="fas fa-redo me-1"></i> Reschedule
                                                    </a>
                                                @endif
                                                @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed' && $appointment->status !== 'approved')
                                                    <button type="button" class="btn btn-sm btn-danger cancel-appointment-btn"
                                                        data-appointment-id="{{ $appointment->id }}"
                                                        data-cancel-url="{{ route('patient.cancel-appointment', $appointment) }}"
                                                        data-appointment-date="{{ $appointment->appointment_date }}"
                                                        data-appointment-time="{{ $appointment->appointment_time }}"
                                                        data-service-type="{{ $appointment->service_type }}">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $appointments->links() }}
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted mb-3">No appointments found.</p>
                        <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary btn-modern">Book Your First
                            Appointment</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
