<div class="filter-card">
    <form method="GET" action="{{ route('superadmin.system-logs') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label text-muted small">Search</label>
            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search..."
                value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small">Action</label>
            <select name="action" id="actionFilter" class="form-select">
                <option value="">All</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small">Table</label>
            <select name="table" id="tableFilter" class="form-select">
                <option value="">All</option>
                @foreach($tables as $table)
                    <option value="{{ $table }}" {{ request('table') == $table ? 'selected' : '' }}>{{ $table }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>
