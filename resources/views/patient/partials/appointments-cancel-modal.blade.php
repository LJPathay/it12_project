<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>Cancel Appointment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Are you sure you want to cancel this appointment?</p>
                <div id="cancelAppointmentDetails">
                    <!-- Details will be populated by JavaScript -->
                </div>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>This action cannot be undone. You will need to book a new appointment if you change your
                        mind.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Keep Appointment
                </button>
                <button type="button" id="confirmCancelBtn" class="btn btn-danger">
                    <i class="fas fa-check me-2"></i>Yes, Cancel It
                </button>
            </div>
        </div>
    </div>
</div>
