@extends('superadmin.layout')

@section('title', 'Backup - Barangay Health Center')
@section('page-title', 'Backup & Restore')
@section('page-description', 'Manage system backups and data restoration')

@section('page-styles')
    <style>
        /* Dark mode styles for backup cards */
        body.bg-dark .backup-card {
            background: #1e2124;
            color: #e6e6e6;
        }

        body.bg-dark .backup-card:hover {
            background: #2a2f35;
        }

        .backup-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            border: none;
            transition: transform 0.2s ease;
        }

        body.bg-dark .backup-card {
            background: #1b1e20;
            border: 1px solid #2a2f35;
            color: #e6e6e6;
        }

        .backup-card:hover {
            transform: translateY(-2px);
        }

        .backup-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .backup-success {
            background-color: #00D100;
            color: #155724;
        }

        body.bg-dark .backup-success {
            background-color: #1e3a28;
            color: #4ade80;
        }

        .backup-warning {
            background-color: #D1D100;
            color: #856404;
        }

        body.bg-dark .backup-warning {
            background-color: #3a3a28;
            color: #fbbf24;
        }

        .backup-danger {
            background-color: #D10000;
            color: #721c24;
        }

        body.bg-dark .backup-danger {
            background-color: #3a2828;
            color: #f87171;
        }

        .backup-info {
            background-color: #009fb1;
            color: #0c5460;
        }

        body.bg-dark .backup-info {
            background-color: #283a3a;
            color: #67e8f9;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
        }
        
        /* Hide Bootstrap pagination's built-in "Showing" text on the left */
        nav[role="navigation"] p,
        nav[role="navigation"] .text-sm {
            display: none !important;
        }
        
        /* Bring showing text closer to pagination */
        #backupsPaginationContainer > div:last-child {
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
@include('superadmin.partials.backup-create')

@include('superadmin.partials.backup-status')

@include('superadmin.partials.backup-list')

    @if($backups->hasPages())
        <div class="d-flex flex-column align-items-center mt-4"
            id="backupsPaginationContainer">
            <div>
                {{ $backups->links('pagination::bootstrap-5') }}
            </div>
            <div class="small text-muted mb-0 mt-n2">
                @if($backups->total() > 0)
                    Showing {{ $backups->firstItem() }}-{{ $backups->lastItem() }} of {{ $backups->total() }} backups
                @else
                    Showing 0 backups
                @endif
            </div>
        </div>
    @endif
@endsection

@push('scripts')
@include('superadmin.partials.backup-scripts')
@endpush