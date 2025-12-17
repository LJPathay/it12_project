@extends('superadmin.layout')

@section('title', 'Malasakit | Archived Users')
@section('page-title', 'Archived Users')
@section('page-description', 'View and manage archived user accounts')

@section('page-styles')
    <style>
        /* Card container - responds to dark mode */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .card {
            background: #1e2124;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        /* Table styles - clean and theme-aware */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            padding: 1rem;
            background: transparent;
        }

        .table.table-dark thead th,
        body.bg-dark .table thead th {
            border-bottom-color: #2a2f35;
            color: #e6e6e6;
            background: transparent !important;
        }

        .table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table.table-dark tbody tr,
        body.bg-dark .table tbody tr {
            border-bottom-color: #2a2f35;
        }

        .table.table-dark tbody tr:hover,
        body.bg-dark .table tbody tr:hover {
            background-color: #2a2f35;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table.table-dark tbody td,
        body.bg-dark .table tbody td {
            color: #e6e6e6;
        }

        /* Pagination */
        .border-top {
            border-top: 1px solid #e9ecef !important;
        }

        body.bg-dark .border-top {
            border-top-color: #2a2f35 !important;
        }

        /* Empty state */
        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }

        body.bg-dark .card-body {
            color: #e6e6e6;
        }
        
        /* Hide Bootstrap pagination's built-in "Showing" text on the left */
        nav[role="navigation"] p,
        nav[role="navigation"] .text-sm {
            display: none !important;
        }
        
        /* Bring showing text closer to pagination */
        #usersArchivePaginationContainer > div:last-child {
            margin-top: -0.5rem !important;
        }
    </style>
@endsection

@push('styles')
    <style>
        /* Hide sidebar on archive page */
        .sidebar {
            display: none !important;
        }

        /* Adjust main content to full width */
        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }

        /* Sidebar dark mode override - loaded AFTER layout styles */
        body.bg-dark .sidebar {
            background: #131516 !important;
            border-right-color: #2a2f35 !important;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('superadmin.users') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i>
                <span>Back to Users</span>
            </a>
        </div>
        <div class="text-muted small">
            Archived users cannot log in until they are restored.
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($users->count() > 0)
@include('superadmin.partials.users-archive-table')
            @else
                <div class="text-center py-5">
                    <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No archived users</h5>
                    <p class="text-muted">Archived users will appear here when you archive them from the Users page.</p>
                </div>
            @endif
        </div>
    </div>

    @if($users->count() > 0)
        <div class="d-flex flex-column align-items-center mt-4"
            id="usersArchivePaginationContainer">
            <div>
                {{ $users->withQueryString()->links() }}
            </div>
            <div class="small text-muted mb-0 mt-n2">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} archived users
            </div>
        </div>
    @endif

@include('superadmin.partials.confirm-modal')
@endsection

@push('scripts')
@include('superadmin.partials.users-archive-scripts')
@include('superadmin.partials.confirm-modal-script')
@endpush