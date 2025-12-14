<div class="appointment-details-body">
    <div class="row">
        <div class="col-md-6">
            <div class="info-section">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-user me-2"></i>Patient Information
                </h6>
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $appointment->patient_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $appointment->patient_phone }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $appointment->patient_address }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-section">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-calendar me-2"></i>Appointment Details
                </h6>
                <div class="info-item">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ $appointment->appointment_date->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Time:</span>
                    <span class="info-value">{{ $appointment->appointment_time }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Service:</span>
                    <span class="info-value">{{ $appointment->service_type }}</span>
                </div>
                @if($appointment->is_walk_in)
                    <div class="info-item">
                        <span class="info-label">Type:</span>
                        <span class="badge bg-secondary">Walk-in</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($appointment->medical_history)
        <div class="info-section">
            <h6 class="fw-bold text-dark mb-3">
                <i class="fas fa-notes-medical me-2"></i>Medical History
            </h6>
            <p class="text-muted mb-0">{{ $appointment->medical_history }}</p>
        </div>
    @endif

    @if($appointment->notes)
        <div class="info-section">
            <h6 class="fw-bold text-dark mb-3">
                <i class="fas fa-sticky-note me-2"></i>Additional Notes
            </h6>
            <p class="text-muted mb-0">{{ $appointment->notes }}</p>
        </div>
    @endif

    @if($appointment->approved_by)
        <div class="info-section">
            <h6 class="fw-bold text-dark mb-3">
                <i class="fas fa-check-circle me-2"></i>Approval Information
            </h6>
            <div class="info-item">
                <span class="info-label">Approved By:</span>
                <span class="info-value">{{ $appointment->approvedBy->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Approved On:</span>
                <span class="info-value">{{ $appointment->approved_at->format('M d, Y g:i A') }}</span>
            </div>
        </div>
    @endif

    <div class="d-flex gap-2 mt-4">
        <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary btn-modern">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
        @if($appointment->status == 'pending')
            <button type="button" class="btn btn-danger btn-modern cancel-appointment-btn"
                data-cancel-url="{{ route('patient.appointment.cancel', $appointment) }}"
                data-appointment-date="{{ $appointment->appointment_date->format('Y-m-d') }}"
                data-appointment-time="{{ $appointment->appointment_time }}"
                data-service-type="{{ $appointment->service_type }}">
                <i class="fas fa-times me-2"></i>Cancel Appointment
            </button>
        @endif
    </div>
</div>
