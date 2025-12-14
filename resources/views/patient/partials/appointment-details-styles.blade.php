@section('page-styles')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .appointment-details-card {
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .appointment-details-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .appointment-details-header {
            background: #fafafa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            margin-bottom: 0;
            border-radius: 12px 12px 0 0;
        }

        body.bg-dark .appointment-details-header {
            background: #1e2124;
            border-color: #2a2f35;
        }

        .appointment-details-body {
            padding: 1.5rem;
        }

        .info-section {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        body.bg-dark .info-section {
            background: #25282c;
            border-color: #2a2f35;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        body.bg-dark .info-item {
            border-bottom-color: #2a2f35;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        body.bg-dark .info-label {
            color: #e6e6e6;
        }

        .info-value {
            color: #6c757d;
        }

        body.bg-dark .info-value {
            color: #b0b0b0;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        body.bg-dark h6.fw-bold.text-dark,
        body.bg-dark h4.fw-bold.text-dark {
            color: #e6e6e6 !important;
        }
    </style>
@endsection
