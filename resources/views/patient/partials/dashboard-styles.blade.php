@section('page-styles')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-card {
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
        }

        body.bg-dark .dashboard-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .dashboard-header {
            background: #fafafa;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 2rem;
            margin-bottom: 0;
            border-radius: 12px 12px 0 0;
        }

        body.bg-dark .dashboard-header {
            background: #1e2124;
            border-color: #2a2f35;
        }

        .dashboard-body {
            padding: 1.5rem;
        }

        .metric-card {
            background: #fafafa;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
        }

        body.bg-dark .metric-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .metric-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        body.bg-dark .metric-number {
            color: #e6e6e6;
        }

        .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        body.bg-dark .metric-label {
            color: #b0b0b0;
        }

        .metric-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .metric-change i {
            font-size: 0.7rem;
        }

        .trend-up {
            animation: trendUp 0.5s ease-out;
        }

        .trend-down {
            animation: trendDown 0.5s ease-out;
        }

        @keyframes trendUp {
            0% {
                transform: translateY(10px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes trendDown {
            0% {
                transform: translateY(-10px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .appointment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #009fb1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .table-modern .text-center {
            padding-right: 4rem;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background-color: #f5f5f5;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
        }

        body.bg-dark .table-modern thead th {
            background-color: #1a1f24;
            color: #e6e6e6;
        }

        .table-modern tbody td {
            border: none;
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
        }

        body.bg-dark .table-modern tbody td {
            border-bottom-color: #2a2f35;
            color: #e6e6e6;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
        }

        body.bg-dark .table-modern tbody tr:hover {
            background-color: #2a2f35;
        }

        body.bg-dark .fw-bold.text-dark {
            color: #e6e6e6 !important;
        }

        /* Empty State Improvements */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        body.bg-dark .empty-state-icon {
            color: #475569;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        body.bg-dark .empty-state-title {
            color: #94a3b8;
        }

        .empty-state-description {
            color: #94a3b8;
            margin-bottom: 2rem;
        }

        body.bg-dark .empty-state-description {
            color: #64748b;
        }

        .btn-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
                transform: scale(1.02);
            }
        }
    </style>
@endsection
