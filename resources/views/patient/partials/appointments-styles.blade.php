@section('page-styles')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .appointments-card {
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .appointments-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .appointments-header {
            background: #fafafa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            margin-bottom: 0;
            border-radius: 12px 12px 0 0;
        }

        body.bg-dark .appointments-header {
            background: #1e2124;
            border-color: #2a2f35;
        }

        body.bg-dark .appointments-header h5 {
            color: #e6e6e6 !important;
        }

        .appointments-body {
            padding: 1.5rem;
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

        .appointment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #009fb1, #008a9a);
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
            color: #000 !important;
        }
        
        .table-modern .text-center {
            text-align: center;
            padding-right: 4rem;
        }
        
        /* Ensure all badge text is black */
        .status-badge.bg-warning,
        .status-badge.bg-success,
        .status-badge.bg-danger,
        .status-badge.bg-info {
            color: #000 !important;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        body.bg-dark .fw-bold.text-dark {
            color: #e6e6e6 !important;
        }

        /* Dark Mode Pagination */
        body.bg-dark .page-link {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .page-item.disabled .page-link {
            background-color: #1a1d20;
            border-color: #2a2f35;
            color: #6c757d;
        }

        body.bg-dark .page-item.active .page-link {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        /* Dark Mode Text Utilities */
        body.bg-dark .text-muted {
            color: #adb5bd !important;
        }

        body.bg-dark .text-dark {
            color: #e6e6e6 !important;
        }

        /* Dark Mode Table Styling */
        body.bg-dark .table-modern {
            color: #e6e6e6;
        }

        body.bg-dark .table-modern td,
        body.bg-dark .table-modern th {
            color: #e6e6e6 !important;
        }

        body.bg-dark .table-modern .appointment-title {
            color: #fff !important;
        }

        body.bg-dark .table-modern .appointment-subtitle {
            color: #adb5bd !important;
        }

        /* Ultra-specific selectors for table content */
        body.bg-dark .appointments-card .table-modern tbody td {
            color: #e6e6e6 !important;
        }

        body.bg-dark .appointments-card .table-modern tbody td *:not(.status-badge):not(.badge) {
            color: inherit !important;
        }

        /* Force status badges to have black text in dark mode */
        body.bg-dark .appointments-card .table-modern tbody td .status-badge,
        body.bg-dark .appointments-card .table-modern tbody td .badge {
            color: #000 !important;
        }

        body.bg-dark .appointments-card .table-modern tbody td div {
            color: #e6e6e6 !important;
        }

        body.bg-dark .appointments-card .table-modern tbody td span:not(.badge):not(.status-badge) {
            color: #e6e6e6 !important;
        }



        /* Filter Chips */
        .filter-chips {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .filter-chip {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            border: 2px solid #e9ecef;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .filter-chip:hover {
            border-color: #009fb1;
            background: rgba(0, 159, 177, 0.1);
        }

        .filter-chip.active {
            background: #009fb1;
            border-color: #009fb1;
            color: white;
        }

        body.bg-dark .filter-chip {
            background: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6 !important;
        }

        body.bg-dark .filter-chip:hover {
            border-color: #009fb1;
            background: rgba(0, 159, 177, 0.2);
            color: #fff !important;
        }

        body.bg-dark .filter-chip.active {
            background: #009fb1;
            border-color: #009fb1;
            color: white !important;
        }

        body.bg-dark .filter-chip i {
            color: inherit;
        }

        /* Search Box Enhancement */
        .search-box {
            position: relative;
        }

        .search-box .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-box input {
            padding-left: 2.5rem;
        }

        .search-box .clear-search {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            display: none;
        }

        .search-box.has-value .clear-search {
            display: block;
        }
    </style>
@endsection
