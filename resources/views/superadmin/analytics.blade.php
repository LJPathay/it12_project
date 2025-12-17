@extends('superadmin.layout')

@section('title', 'Malasakit | Analytics')
@section('page-title', 'Analytics')
@section('page-description', 'System usage and performance metrics')

@section('page-styles')
    <style>
        /* Theme-aware text */
        body {
            color: #111;
        }

        body.bg-dark {
            color: #fff;
        }

        body.bg-dark .stat-label {
            color: #cbd3da;
        }

        body.bg-dark .chart-container {
            background: #1e2124;
            color: #e6e6e6;
        }

        .stat-number {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.75rem;
            margin-bottom: 0;
        }

        /* Make stats cards more visible */
        .stats-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stats-card:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        body.bg-dark .stats-card {
            background: #2a2f35;
            border: 1px solid #3a3f45;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        body.bg-dark .stats-card:hover {
            background: #343940;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 0.5rem;
            height: 500px;
        }

        .chart-container canvas {
            max-height: 460px !important;
        }
    </style>
@endsection

@push('styles')
    <style>
        body.bg-dark .sidebar {
            background: #131516 !important;
            border-right-color: #2a2f35 !important;
        }
    </style>
@endpush

@section('content')
@include('superadmin.partials.analytics-stats')

@include('superadmin.partials.analytics-charts')
@endsection

@push('scripts')
@include('superadmin.partials.analytics-scripts')
@endpush