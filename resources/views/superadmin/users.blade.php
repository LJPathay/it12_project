@extends('superadmin.layout')

@section('title', 'Malasakit | User Management')
@section('page-title', 'User Management')
@section('page-description', 'View and manage all registered users')

@section('page-styles')
    <style>
        /* User cards */
        .patient-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            border: none;
            transition: transform 0.2s ease;
        }

        .patient-card:hover {
            transform: translateY(-2px);
        }

        body.bg-dark .patient-card {
            background: #1e2124;
            color: #e6e6e6;
        }

        body.bg-dark .patient-card:hover {
            background: #2a2f35;
        }

        /* Status badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            min-width: 90px;
            line-height: 1;
        }

        .status-active {
            background-color: #00D100;
            color: #fff;
        }

        .status-inactive {
            background-color: #D10000;
            color: #fff;
        }

        .status-user {
            background-color: #009fb1;
            color: #fff;
        }

        .status-admin {
            background-color: #D1D100;
            color: #fff;
        }

        .status-superadmin {
            background-color: #D10000;
            color: #fff;
        }

        /* Patient avatar */
        .patient-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #009fb1, #008a9a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Modal */
        .modal-content {
            border-radius: 12px;
            border: none;
        }

        /* Form controls */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #009fb1;
            box-shadow: 0 0 0 0.2rem rgba(0, 159, 177, 0.25);
        }

        /* Role cell alignment */
        .role-cell {
            text-align: center;
            vertical-align: middle !important;
        }

        /* Action buttons */
        .action-btn {
            min-width: 110px;
        }

        /* Table container - responds to dark mode */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        body.bg-dark .table-container {
            background: #1e2124;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        /* Table styles - clean and theme-aware */
        .users-table {
            margin-bottom: 0;
        }

        .users-table thead th {
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            padding: 1rem;
            background: transparent;
        }

        .users-table.table-dark thead th,
        body.bg-dark .users-table thead th {
            border-bottom-color: #2a2f35;
            color: #e6e6e6;
        }

        .users-table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .users-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .users-table.table-dark tbody tr,
        body.bg-dark .users-table tbody tr {
            border-bottom-color: #2a2f35;
        }

        .users-table.table-dark tbody tr:hover,
        body.bg-dark .users-table tbody tr:hover {
            background-color: #2a2f35;
        }

        .users-table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .users-table.table-dark tbody td,
        body.bg-dark .users-table tbody td {
            color: #e6e6e6;
        }

        /* Pagination */
        .pagination-container {
            border-top: 1px solid #e9ecef;
            padding: 1rem;
        }

        body.bg-dark .pagination-container {
            border-top-color: #2a2f35;
        }

        /* Action buttons */
        .users-table .btn-outline-secondary {
            border-color: #dee2e6;
            color: #6c757d;
        }

        .users-table .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
        }

        body.bg-dark .users-table .btn-outline-secondary {
            border-color: #2a2f35;
            color: #adb5bd;
        }

        body.bg-dark .users-table .btn-outline-secondary:hover {
            background-color: #2a2f35;
            border-color: #495057;
            color: #e6e6e6;
        }
        
        /* Dark mode for search input group */
        body.bg-dark .input-group-text {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }
        
        body.bg-dark .form-control,
        body.bg-dark .form-select {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }
        
        body.bg-dark .form-control:focus,
        body.bg-dark .form-select:focus {
            background-color: #2a2f35;
            border-color: #009fb1;
            color: #e6e6e6;
        }
        
        body.bg-dark .form-control::placeholder {
            color: #6c757d;
        }
        
        /* Hide Bootstrap pagination's built-in "Showing" text on the left */
        nav[role="navigation"] p,
        nav[role="navigation"] .text-sm,
        nav p,
        .pagination-wrapper p,
        #usersPaginationContainer nav p,
        #usersPaginationContainer p:first-child,
        #usersPaginationContainer > p {
            display: none !important;
        }
        
        /* Bring showing text closer to pagination */
        #usersPaginationContainer > div:last-child {
            margin-top: -0.5rem !important;
        }
    </style>
@endsection

@push('styles')
    <style>
        /* Sidebar dark mode override - loaded AFTER layout styles */
        body.bg-dark .sidebar {
            background: #131516 !important;
            border-right-color: #2a2f35 !important;
        }
    </style>
@endpush

@section('content')
    <!-- Add User + Archive Buttons -->
    <div class="d-flex justify-content-end align-items-center mb-4 gap-2">
        <a href="{{ route('superadmin.users.archive') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="fas fa-archive me-2"></i>
            <span>Archive History</span>
        </a>
        <button class="btn btn-primary d-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="fas fa-user-shield me-2"></i>
            <span>Add Admin</span>
        </button>
    </div>

    <!-- Search and Filter (client-side) -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="userSearch" placeholder="Search users by name or email..."
                    value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="roleFilter">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center"
                id="clearFiltersBtn">
                <i class="fas fa-times me-2"></i>
                <span>Clear</span>
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-container" id="usersTableContainer">
        @if($users->count() > 0)
            @include('superadmin.partials.users-table')
            
            <!-- Modals for EACH user are placed here so they are refreshed with AJAX -->
            @include('superadmin.partials.user-modals')
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No users found</h5>
                <p class="text-muted">There are no registered users at the moment.</p>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($users->count() > 0)
        <div id="usersPaginationContainer" class="mt-4"></div>
    @endif
    <!-- Confirm Action Modal -->
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmActionTitle">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmActionMessage">
                    Are you sure?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Add Admin Modal -->
@include('superadmin.modals.add-admin')

<!-- Add Patient Modal -->
@include('superadmin.modals.add-patient')

@push('scripts')
    @include('superadmin.partials.users-scripts')
    @if($users->count() > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TablePaginator({
                tableId: 'usersTableBody',
                tableBodyId: 'usersTableBody',
                paginationContainerId: 'usersPaginationContainer',
                searchId: 'userSearch',
                filterInputs: {
                    roleFilter: (row, roleToCheck) => {
                        if (!roleToCheck) return true;
                        // Role is in the 3rd column (index 2)
                        const roleCell = row.cells[2].textContent.trim().toLowerCase();
                        return roleCell.includes(roleToCheck.toLowerCase());
                    }
                },
                rowsPerPage: 10
            });
        });
    </script>
    @endif
@endpush
