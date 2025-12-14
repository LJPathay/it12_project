<!-- Appointments Table -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h5 class="mb-0">My Appointments</h5>
            </div>
            <div class="dashboard-body p-0">
                @if($appointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
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
                                @foreach($appointments->take(10) as $appointment)
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
                                                                                                                @if($appointment->status == 'pending') bg-warning text-dark
                                                                                                                @elseif($appointment->status == 'approved') bg-success text-white
                                                                                                                @elseif($appointment->status == 'completed') bg-info text-white
                                                                                                                @elseif($appointment->status == 'cancelled') bg-danger text-white
                                                                                                                @else bg-secondary text-white
                                                                                                                @endif">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('patient.appointment.show', $appointment) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                @if($appointment->status == 'pending')
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($appointments->count() > 10)
                        <div class="text-center p-3 border-top">
                            <a href="{{ route('patient.appointments') }}" class="btn btn-primary">
                                <i class="fas fa-calendar me-2"></i> View All Appointments
                            </a>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="empty-state-title">No Appointments Yet</h3>
                        <p class="empty-state-description">
                            You haven't booked any appointments yet. Start your healthcare journey by scheduling your first
                            visit.
                        </p>
                        <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary btn-pulse">
                            <i class="fas fa-plus me-2"></i>Book Your First Appointment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
