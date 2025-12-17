<!-- Quick Stats -->
<div class="row mb-4 g-3">
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="metric-label">Pending Appointments</div>
                    <div class="metric-number">{{ $appointments->where('status', 'pending')->count() }}</div>
                    <div class="metric-change text-warning trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span class="fw-bold">Awaiting Approval</span>
                    </div>
                </div>
                <div class="text-warning">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="metric-label">Approved Appointments</div>
                    <div class="metric-number">{{ $appointments->where('status', 'approved')->count() }}</div>
                    <div class="metric-change text-success trend-up">
                        <i class="fas fa-check-circle"></i>
                        <span class="fw-bold">Ready for Visit</span>
                    </div>
                </div>
                <div class="text-success">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>
