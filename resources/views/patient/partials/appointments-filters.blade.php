<!-- Filter Chips -->
<div class="row mb-3">
    <div class="col-12">
        <div class="filter-chips">
            <button class="filter-chip active" data-filter="all">
                <i class="fas fa-list me-1"></i> All
            </button>
            <button class="filter-chip" data-filter="pending">
                <i class="fas fa-clock me-1"></i> Pending
            </button>
            <button class="filter-chip" data-filter="approved">
                <i class="fas fa-check-circle me-1"></i> Approved
            </button>
            <button class="filter-chip" data-filter="completed">
                <i class="fas fa-check-double me-1"></i> Completed
            </button>
            <button class="filter-chip" data-filter="cancelled">
                <i class="fas fa-times-circle me-1"></i> Cancelled
            </button>
        </div>
    </div>
</div>

<!-- Search Box -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="appointmentSearch" class="form-control"
                placeholder="Search by appointment ID, service type...">
            <button class="clear-search" id="clearSearch">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>
