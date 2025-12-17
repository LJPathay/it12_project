<!-- Quick Stats -->
<div class="row mb-4 g-3">
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-label fw-bold">Pending Appointments</div>
                    @php
                        $today = \Carbon\Carbon::today();
                        $pendingCount = $appointments->where('status', 'pending')->filter(function ($appointment) use ($today) {
                            return \Carbon\Carbon::parse($appointment->appointment_date)->gte($today);
                        })->count();
                    @endphp
                    <div class="kpi-value">{{ $pendingCount }}</div>
                    @if($pendingCount > 0)
                    <div class="text-warning">
                        <i class="fas fa-clock"></i>
                        <span>Awaiting Approval</span>
                    </div>
                    @endif
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
                    <div class="kpi-label fw-bold">Approved Appointments</div>
                    @php
                        $approvedCount = $appointments->where('status', 'approved')->filter(function ($appointment) use ($today) {
                            return \Carbon\Carbon::parse($appointment->appointment_date)->gte($today);
                        })->count();
                    @endphp
                    <div class="kpi-value">{{ $approvedCount }}</div>
                    @if($approvedCount > 0)
                    <div class="text-success">
                        <i class="fas fa-check-circle"></i>
                        <span>Ready for Visit</span>
                    </div>
                    @endif
                </div>
                <div class="text-success">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>
