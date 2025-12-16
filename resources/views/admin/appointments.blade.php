@extends('admin.layout')

@section('title', 'Appointments - Barangay Health Center')
@section('page-title', 'Manage Appointments')
@section('page-description', 'View and manage all patient appointments')

@section('page-styles')
    <style>
        .appointment-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            margin-bottom: 1rem;
            border: 1px solid #edf1f7;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .appointment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
            border-color: #d0e2ff;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: #D1C700;
            color: #000000;
        }

        .status-approved {
            background-color: #77dd77;
            color: #000000;
        }

        .status-completed {
            background-color: #d1ecf1;
            color: #000000;
        }

        .status-cancelled {
            background-color: #F53838;
            color: #000000;
        }

        .status-no_show {
            background-color: #e2e3e5;
            color: #000000;
        }

        .status-rescheduled {
            background-color: #ffc107;
            color: #000000;
        }

        .status-blocked {
            background-color: #343a40;
            color: #ffffff;
        }

        .status-in_progress {
            background-color: #0d6efd;
            color: #ffffff;
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background-color: #f9fafb;
            border: none;
            font-weight: 600;
            color: #4b5563;
            padding: 0.9rem 1rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: .04em;
        }

        .table-modern tbody td {
            border: none;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #edf2f7;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Ensure dropdowns inside responsive tables are not clipped */
        .table-responsive {
            overflow: visible;
        }

        /* Keep actions column flexible */
        .actions-col {
            white-space: nowrap;
        }

        /* Dark mode modal + form fields (booking drawer/modal) */
        body.bg-dark .offcanvas,
        body.bg-dark .modal-content {
            background: #1e2124;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .offcanvas .form-label,
        body.bg-dark .modal-content .form-label {
            color: #e6e6e6;
        }

        body.bg-dark .offcanvas .form-control,
        body.bg-dark .offcanvas .form-select,
        body.bg-dark .modal-content .form-control,
        body.bg-dark .modal-content .form-select {
            background-color: #0f1316;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .offcanvas .form-control::placeholder,
        body.bg-dark .modal-content .form-control::placeholder {
            color: #9aa4ad;
        }

        body.bg-dark .offcanvas .input-group-text {
            background: #1a1f24;
            color: #cbd3da;
            border-color: #2a2f35;
        }

        /* Dark mode dropdown */
        body.bg-dark .dropdown-menu {
            background: #1e2124;
            border-color: #2a2f35;
        }

        body.bg-dark .dropdown-item {
            color: #e6e6e6;
        }

        body.bg-dark .dropdown-item:hover,
        body.bg-dark .dropdown-item.active {
            background-color: #2a2f35;
            color: #fff;
        }

        body.bg-dark .table-modern thead th {
            background-color: #1a1f24;
            color: #e6e6e6;
        }

        body.bg-dark .table-modern tbody td {
            border-bottom-color: #2a2f35;
            color: #d6d6d6;
        }

        body.bg-dark .table-modern tbody tr:hover {
            background-color: #2a2f35;
        }

        /* Match Inventory's darker sidebar color in dark mode */
        body.bg-dark .sidebar {
            background: #131516;
            border-right-color: #2a2f35;
        }

        /* Calendar Styles */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            font-size: 0.8rem;
        }

        .calendar-header {
            text-align: center;
            font-weight: 600;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .calendar-day:hover {
            background-color: #e9ecef;
        }

        .calendar-day.selected {
            background-color: #009fb1;
            color: white;
            border-color: #009fb1;
        }

        .calendar-day.occupied {
            background-color: #F53838;
            color: #000;
            border-color: #F53838;
        }

        .calendar-day.partially-occupied {
            background-color: #D1C700;
            color: #000;
            border-color: #D1C700;
        }

        .calendar-day.weekend {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .calendar-day.past {
            background-color: #e9ecef;
            color: #adb5bd;
            cursor: not-allowed;
        }

        .calendar-day .day-number {
            font-weight: 600;
            font-size: 0.9rem;
            z-index: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 70%;
        }

        .calendar-day .slot-indicator {
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.55rem;
            background: rgba(0, 0, 0, 0.15);
            color: #666;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: 600;
            z-index: 2;
            line-height: 1;
            width: auto;
            white-space: nowrap;
        }

        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.5rem;
        }

        .time-slot {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .time-slot.available {
            background-color: #77dd77;
            border-color: #66cc66;
            color: #000;
        }

        .time-slot.available:hover {
            background-color: #66cc66;
        }

        .time-slot.occupied {
            background-color: #F53838;
            border-color: #e62929;
            color: #000;
            cursor: not-allowed;
        }

        .time-slot.past {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .time-slot.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: white;
        }

        .time-slot .time {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .time-slot .status {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        body.bg-dark .calendar-header {
            background-color: #2a2f35;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .calendar-day {
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .calendar-day:hover {
            background-color: #2a2f35;
        }

        body.bg-dark .calendar-day.weekend {
            background-color: #1e2124;
            color: #6c757d;
        }

        body.bg-dark .calendar-day.past {
            background-color: #1e2124;
            color: #6c757d;
        }

        body.bg-dark .time-slot {
            border-color: #2a2f35;
        }

        body.bg-dark .time-slot.available {
            background-color: #77dd77;
            border-color: #66cc66;
            color: #000;
        }

        body.bg-dark .time-slot.occupied {
            background-color: #F53838;
            border-color: #e62929;
            color: #000;
        }

        body.bg-dark .time-slot.past {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #6c757d;
            opacity: 0.5;
        }

        /* Skeleton Loading */
        .skeleton {
            background: #eee;
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            border-radius: 5px;
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
        }

        body.bg-dark .skeleton {
            background: #2a2f35;
            background: linear-gradient(110deg, #2a2f35 8%, #32383e 18%, #2a2f35 33%);
            background-size: 200% 100%;
        }

        @keyframes shine {
            to {
                background-position-x: -200%;
            }
        }

        .calendar-skeleton {
            height: 300px;
            width: 100%;
        }

        /* Row Status Colors */
        tr[data-status="pending"] {
            background-color: rgba(255, 243, 205, 0.3);
        }

        tr[data-status="approved"] {
            background-color: rgba(212, 237, 218, 0.3);
        }

        tr[data-status="completed"] {
            background-color: rgba(209, 236, 241, 0.3);
        }

        tr[data-status="cancelled"] {
            background-color: rgba(248, 215, 218, 0.3);
        }

        tr[data-status="no_show"] {
            background-color: rgba(226, 227, 229, 0.3);
        }

        tr[data-status="rescheduled"] {
            background-color: rgba(255, 243, 205, 0.3);
        }

        /* Dark Mode Row Status Colors */
        body.bg-dark tr[data-status="pending"] {
            background-color: rgba(133, 100, 4, 0.15);
        }

        body.bg-dark tr[data-status="approved"] {
            background-color: rgba(21, 87, 36, 0.15);
        }

        body.bg-dark tr[data-status="completed"] {
            background-color: rgba(12, 84, 96, 0.15);
        }

        body.bg-dark tr[data-status="cancelled"] {
            background-color: rgba(114, 28, 36, 0.15);
        }

        body.bg-dark tr[data-status="no_show"] {
            background-color: rgba(56, 61, 65, 0.15);
        }

        body.bg-dark tr[data-status="rescheduled"] {
            background-color: rgba(133, 100, 4, 0.15);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            line-height: 1;
        }

        .btn-icon i {
            margin: 0;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    <style>
        /* Dark Mode Overrides */
        body.bg-dark .appointment-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        body.bg-dark .text-muted {
            color: #adb5bd !important;
        }

        body.bg-dark .fw-bold {
            color: #e6e6e6 !important;
        }

        /* Dark Mode Pagination */
        body.bg-dark .pagination .page-link {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .pagination .page-link:hover {
            background-color: #2a2f35;
            border-color: #3f4751;
            color: #ffffff;
        }

        body.bg-dark .pagination .page-item.active .page-link {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: #ffffff;
        }

        body.bg-dark .pagination .page-item.disabled .page-link {
            background-color: #1a1f24;
            border-color: #2a2f35;
            color: #6c757d;
        }

        /* Hide Bootstrap pagination's built-in "Showing" text on the left */
        nav[role="navigation"] p,
        nav[role="navigation"] .text-sm {
            display: none !important;
        }

        /* Bring showing text closer to pagination */
        #appointmentsPaginationContainer>div:last-child {
            margin-top: -0.5rem !important;
        }

        /* Dark Mode Calendar & Time Slots */
        body.bg-dark .calendar-header {
            background-color: #2a2f35;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .calendar-day {
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .calendar-day:hover {
            background-color: #2a2f35;
        }

        body.bg-dark .calendar-day.weekend {
            background-color: #1e2124;
            color: #6c757d;
        }

        body.bg-dark .calendar-day.past {
            background-color: #1e2124;
            color: #6c757d;
        }

        body.bg-dark .time-slot {
            border-color: #2a2f35;
        }

        body.bg-dark .time-slot.available {
            background-color: #77dd77;
            border-color: #66cc66;
            color: #000;
        }

        body.bg-dark .time-slot.occupied {
            background-color: #F53838;
            border-color: #e62929;
            color: #000;
        }

        body.bg-dark .time-slot.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        /* Dark Mode Modal & Forms */
        body.bg-dark .modal-content {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .modal-header {
            border-bottom-color: #2a2f35;
        }

        body.bg-dark .modal-footer {
            border-top-color: #2a2f35;
        }

        body.bg-dark .modal-content .card {
            background-color: #16191c;
            border-color: #2a2f35;
        }

        body.bg-dark .modal-content .card-header {
            background-color: #1f2327;
            border-bottom-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .form-control,
        body.bg-dark .form-select {
            background-color: #0f1316;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .form-control:focus,
        body.bg-dark .form-select:focus {
            background-color: #161b20;
            border-color: #009fb1;
            color: #e6e6e6;
        }

        body.bg-dark .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Dark Mode Table */
        body.bg-dark .table-modern thead th {
            background-color: #1a1f24;
            color: #e6e6e6;
        }

        body.bg-dark .table-modern tbody td {
            border-bottom-color: #2a2f35;
            color: #d6d6d6;
        }

        body.bg-dark .table-modern tbody tr:hover {
            background-color: #2a2f35;
        }

        /* Polished Modal Styling */
        .info-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }

        .info-item {
            position: relative;
        }

        .info-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 0.95rem;
            color: #212529;
            font-weight: 500;
        }

        .notes-box {
            background: #ffffff;
            border: 1px solid #dee2e6;
            font-size: 0.9rem;
            color: #495057;
            line-height: 1.6;
        }

        .timestamp-item {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .timestamp-label {
            font-weight: 600;
            margin-right: 0.25rem;
        }

        .timestamp-value {
            color: #495057;
        }

        /* Dark Mode Modal Styling */
        body.bg-dark .info-card {
            background: #2a2f35;
            border-color: #3f4751;
        }

        body.bg-dark .info-label {
            color: #adb5bd;
        }

        body.bg-dark .info-value {
            color: #e9ecef;
        }

        body.bg-dark .notes-box {
            background: #1a1f24;
            border-color: #3f4751;
            color: #cbd3da;
        }

        body.bg-dark .timestamp-item {
            color: #adb5bd;
        }

        body.bg-dark .timestamp-value {
            color: #cbd3da;
        }

        body.bg-dark .modal-footer {
            background-color: #1a1f24 !important;
            border-top-color: #2a2f35;
        }

        /* Modern Button Styles */
        .btn-modern {
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1.5;
            cursor: pointer;
        }

        .btn-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-modern:active {
            transform: translateY(0);
        }

        /* Success Button */
        .btn-modern-success {
            background: #10b981;
            color: white;
        }

        .btn-modern-success:hover {
            background: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        /* Warning Button */
        .btn-modern-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-modern-warning:hover {
            background: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        /* Danger Button */
        .btn-modern-danger {
            background: #ef4444;
            color: white;
        }

        .btn-modern-danger:hover {
            background: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* Secondary Button */
        .btn-modern-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-modern-secondary:hover {
            background: #4b5563;
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
        }

        /* Outline Button */
        .btn-modern-outline {
            background: transparent;
            color: #6b7280;
            border: 2px solid #d1d5db;
        }

        .btn-modern-outline:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
            color: #374151;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Dark Mode Button Styles */
        body.bg-dark .btn-modern-outline {
            color: #cbd3da;
            border-color: #3f4751;
        }

        body.bg-dark .btn-modern-outline:hover {
            background: #2a2f35;
            border-color: #4b5563;
            color: #e9ecef;
        }

        /* Dark Mode for Add Appointment Modal */
        body.bg-dark #appointmentSearchResults {
            background: #1e2124;
            border-color: #2a2f35;
        }

        body.bg-dark #appointmentSearchResults .list-group-item {
            background: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark #appointmentSearchResults .list-group-item:hover {
            background: #2a2f35;
        }

        body.bg-dark #appointmentSearchResults .list-group-item .text-muted,
        body.bg-dark #appointmentSearchResults .list-group-item .small {
            color: #adb5bd !important;
        }

        body.bg-dark #appointmentSelectedPatientInfo {
            background-color: rgba(23, 162, 184, 0.2);
            border-color: rgba(23, 162, 184, 0.4);
            color: #cbd3da;
        }

        body.bg-dark .badge.bg-secondary {
            background-color: #4b5563 !important;
        }

        /* ========== RESPONSIVE STYLES FOR MOBILE ========== */

        /* Tablet and below (≤991px) */
        @media (max-width: 991px) {

            /* Ensure content doesn't get cut off */
            .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            /* Appointment cards on tablet */
            .appointment-card {
                padding: 1rem;
                margin-bottom: 0.75rem;
            }

            /* Filter panel adjustments */
            .row.mb-4 {
                margin-bottom: 1rem !important;
            }

            .col-md-3,
            .col-md-6,
            .col-md-9 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 0.75rem;
            }

            /* Button spacing */
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            /* Table responsive */
            .table-responsive {
                margin: 0 -0.75rem;
                padding: 0 0.75rem;
            }

            /* Reduce table font size */
            .table-modern {
                font-size: 0.875rem;
            }

            .table-modern thead th,
            .table-modern tbody td {
                padding: 0.6rem 0.75rem;
            }
        }

        /* Mobile phones (≤768px) */
        @media (max-width: 768px) {

            /* More compact cards */
            .appointment-card {
                padding: 0.75rem;
                border-radius: 10px;
            }

            /* Status badges smaller */
            .status-badge {
                padding: 0.35rem 0.75rem;
                font-size: 0.75rem;
            }

            /* Filter section */
            .filter-card,
            .card {
                margin-bottom: 0.75rem;
            }

            /* Form controls */
            .form-control,
            .form-select {
                padding: 0.55rem 0.75rem;
                font-size: 16px;
                /* Prevents zoom on iOS */
            }

            /* Buttons full width in filter section */
            .d-flex.gap-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }

            .d-flex.gap-2 .btn {
                width: 100%;
            }

            /* Table adjustments */
            .table-modern {
                font-size: 0.8rem;
            }

            .table-modern thead th,
            .table-modern tbody td {
                padding: 0.5rem;
            }

            /* Hide some table columns on mobile */
            .table-modern .d-md-table-cell {
                display: none !important;
            }

            /* Action buttons smaller */
            .btn-icon {
                width: 28px;
                height: 28px;
            }

            .btn-icon i {
                font-size: 0.8rem;
            }

            /* Stack action buttons */
            .d-flex.gap-1 {
                gap: 0.25rem !important;
            }

            /* Pagination */
            .pagination {
                font-size: 0.8rem;
            }

            .page-link {
                padding: 0.4rem 0.6rem;
            }

            /* Modal responsive adjustments */
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100vw - 1rem);
                width: calc(100vw - 1rem);
            }

            .modal-content {
                border-radius: 10px;
                max-height: calc(100vh - 1rem);
            }

            .modal-header {
                padding: 1rem;
            }

            .modal-body {
                padding: 1rem;
                max-height: calc(100vh - 180px);
                overflow-y: auto;
            }

            .modal-footer {
                padding: 1rem;
                flex-wrap: wrap;
            }

            .modal-footer .btn {
                flex: 1 1 auto;
                min-width: 120px;
            }

            /* Calendar in modal */
            .calendar-grid {
                font-size: 0.7rem;
                gap: 1px;
            }

            .calendar-header {
                padding: 0.3rem;
                font-size: 0.7rem;
            }

            .calendar-day {
                min-height: 32px;
                font-size: 0.7rem;
            }

            /* Time slots */
            .time-slots-grid {
                grid-template-columns: 1fr;
                gap: 0.4rem;
            }

            .time-slot {
                padding: 0.5rem;
            }

            .time-slot .time {
                font-size: 0.85rem;
            }

            .time-slot .status {
                font-size: 0.7rem;
            }
        }

        /* Small mobile (≤576px) */
        @media (max-width: 576px) {

            /* Ultra compact appointments */
            .appointment-card {
                padding: 0.6rem;
            }

            /* Very small status badges */
            .status-badge {
                padding: 0.3rem 0.6rem;
                font-size: 0.7rem;
            }

            /* Compact table */
            .table-modern {
                font-size: 0.75rem;
            }

            .table-modern thead th,
            .table-modern tbody td {
                padding: 0.4rem 0.3rem;
            }

            /* Action buttons very compact */
            .btn-icon {
                width: 26px;
                height: 26px;
            }



            /* ===== MODAL RESPONSIVE FIXES ===== */
            /* Make modals nearly full-screen on very small devices */
            .modal-dialog {
                margin: 0.25rem;
                max-width: calc(100vw - 0.5rem);
                width: calc(100vw - 0.5rem);
            }

            .modal-content {
                border-radius: 8px;
                max-height: calc(100vh - 0.5rem);
            }

            .modal-header {
                padding: 0.75rem 1rem;
            }

            .modal-title {
                font-size: 1rem;
            }

            .modal-body {
                padding: 0.75rem;
                max-height: calc(100vh - 200px);
                overflow-y: auto;
            }

            .modal-footer {
                padding: 0.75rem;
                flex-direction: column-reverse;
                gap: 0.5rem;
            }

            .modal-footer .btn {
                width: 100%;
                margin: 0;
            }

            /* Info cards in modals */
            .info-card {
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .info-label {
                font-size: 0.7rem;
                margin-bottom: 0.35rem;
            }

            .info-value {
                font-size: 0.875rem;
            }


        /* Modal full screen on very small devices */
        .modal-dialog {
            margin: 0;
            max-width: 100%;
            min-height: 100vh;
        }

        .modal-content {
            min-height: 100vh;
            border-radius: 0;
        }

        /* Offcanvas adjustments */
        .offcanvas {
            max-width: 100% !important;
        }

        /* Form labels smaller */
        .form-label {
            font-size: 0.85rem;
        }

        /* Calendar very compact */
        .calendar-grid {
            font-size: 0.65rem;
        }

        .calendar-day {
            min-height: 28px;
        }

        /* Info cards in modals */
        .info-card {
            margin-bottom: 0.75rem;
            padding: 0.75rem;
        }

        .info-label {
            font-size: 0.7rem;
        }

        .info-value {
            font-size: 0.85rem;
        }

        /* Button groups stack */
        .modal-footer .btn-group,
        .modal-footer .d-flex {
            flex-direction: column-reverse;
            width: 100%;
        }

        .modal-footer .btn {
            width: 100%;
            margin: 0.25rem 0;
        }
        }

        /* Prevent horizontal scrolling */
        @media (max-width: 991px) {

            body,
            html {
                overflow-x: hidden;
                max-width: 100vw;
            }

            .row {
                margin-left: 0;
                margin-right: 0;
            }

            .row>* {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            /* Ensure all elements fit */
            *,
            *::before,
            *::after {
                max-width: 100%;
            }

            /* Table container */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {

            /* Larger touch targets */
            .btn,
            .page-link,
            .btn-icon,
            .dropdown-toggle {
                min-height: 44px;
                min-width: 44px;
            }

            /* Calendar days */
            .calendar-day {
                min-height: 44px !important;
            }

            /* Time slots */
            .time-slot {
                min-height: 44px;
            }

            /* Remove hover effects */
            .appointment-card:hover {
                transform: none;
            }

            .table-modern tbody tr:hover {
                background-color: inherit;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Add Appointment Button -->

    <!-- Top Actions -->
    <div class="d-flex flex-wrap justify-content-end align-items-center mb-3 gap-2">
        <div class="d-flex align-items-center gap-2">
            <!-- Bulk Actions (hidden by default) -->
            <div id="bulkActions" class="d-none d-flex align-items-center gap-2">
                <span class="text-muted" id="selectedCount">0 selected</span>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-success" id="bulkApprove">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="bulkCancel">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" id="bulkComplete">
                        <i class="fas fa-check-circle me-1"></i> Complete
                    </button>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#blockDateModal">
                <i class="fas fa-ban me-2"></i> Block Date
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                <i class="fas fa-plus me-2"></i> Add New Appointment
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" id="appointmentStatusFilter">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="rescheduled">Rescheduled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Service</label>
                <select class="form-select" id="appointmentServiceFilter">
                    <option value="">All</option>
                    @foreach(($services ?? []) as $service)
                        <option value="{{ strtolower($service) }}">{{ $service }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" id="appointmentSearch" class="form-control"
                    placeholder="Search by patient, email, or phone">
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-outline-secondary w-100" id="appointmentFiltersReset">
                    <i class="fas fa-undo me-1"></i> Clear
                </button>
            </div>
        </div>
        <div class="mt-2 small text-muted" id="appointmentsFilterSummary">
            Showing {{ $appointments->count() }} appointments
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="table-card p-0">
        <div class="table-responsive d-none d-md-block">
            @php
                $currentSort = request('sort');
                $currentDirection = strtolower(request('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
                $nextDirection = $currentSort === 'date' && $currentDirection === 'asc' ? 'desc' : 'asc';
            @endphp
            <table class="table table-modern table-hover mb-0 align-middle">
                <thead class="table-light position-sticky top-0" style="z-index:1;">
                    <tr>
                        <th style="width:30px"><input type="checkbox" id="selectAll"></th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => $nextDirection]) }}"
                                class="text-decoration-none d-inline-flex align-items-center gap-1">
                                <span style="color: #009fb1;">Date</span>
                                @if($currentSort === 'date')
                                    <i class="fas fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} fa-sm ms-1"
                                        style="color: #009fb1;"></i>
                                @else
                                    <i class="fas fa-sort fa-sm ms-1" style="color: #009fb1;"></i>
                                @endif
                            </a>
                        </th>
                        <th>Patient</th>
                        <th>Service</th>

                        <th class="text-center">Status</th>
                        <th class="text-center actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody id="appointmentsTableBody">
                    @forelse($appointments as $appointment)
                        <tr data-status="{{ strtolower($appointment->status) }}"
                            data-service="{{ strtolower($appointment->service_type) }}"
                            data-patient="{{ strtolower($appointment->patient_name) }}"
                            data-email="{{ strtolower($appointment->user->email ?? '') }}"
                            data-phone="{{ strtolower($appointment->patient_phone ?? '') }}">
                            <td><input type="checkbox" class="row-check" value="{{ $appointment->id }}"
                                    data-appointment-id="{{ $appointment->id }}"></td>
                            <td>
                                <div class="fw-bold">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                <small
                                    class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $appointment->patient_name }}</div>
                                <small class="text-muted">{{ $appointment->user->email ?? $appointment->patient_phone }}</small>
                            </td>
                            <td>{{ $appointment->service_type }}</td>

                            <td class="text-center">
                                @php
                                    $statusDisplay = [
                                        'pending' => 'Pending',
                                        'approved' => 'Confirmed',
                                        'rescheduled' => 'Rescheduled',
                                        'cancelled' => 'Cancelled',
                                        'completed' => 'Completed',
                                        'no_show' => 'No Show',
                                        'blocked' => 'Blocked',
                                        'in_progress' => 'In Progress'
                                    ][$appointment->status] ?? ucfirst(str_replace('_', ' ', $appointment->status));
                                @endphp
                                <span class="status-badge status-{{ $appointment->status }}">{{ $statusDisplay }}</span>
                            </td>
                            <td class="text-center actions-col">
                                <div class="d-flex gap-1 justify-content-center">
                                    <!-- View Button -->
                                    <button class="btn btn-outline-primary btn-sm btn-icon" data-bs-toggle="modal"
                                        data-bs-target="#viewAppointmentModal{{ $appointment->id }}" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>



                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="mb-2">No appointments match your filters.</div>
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">Add an
                                    appointment</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Visible on small screens) -->
        <div class="d-md-none p-3">
            @forelse($appointments as $appointment)
                <div class="appointment-card mb-3 p-3 border rounded shadow-sm bg-white"
                    data-status="{{ strtolower($appointment->status) }}"
                    data-service="{{ strtolower($appointment->service_type) }}"
                    data-patient="{{ strtolower($appointment->patient_name) }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold">{{ $appointment->patient_name }}</div>
                            <div class="small text-muted">{{ $appointment->service_type }}</div>
                        </div>
                        @php
                            $statusDisplay = [
                                'pending' => 'Pending',
                                'approved' => 'Confirmed',
                                'rescheduled' => 'Rescheduled',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                                'no_show' => 'No Show',
                                'blocked' => 'Blocked',
                                'in_progress' => 'In Progress'
                            ][$appointment->status] ?? ucfirst(str_replace('_', ' ', $appointment->status));
                        @endphp
                        <span class="status-badge status-{{ $appointment->status }}">{{ $statusDisplay }}</span>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex align-items-center text-muted small mb-1">
                            <i class="fas fa-calendar-alt me-2" style="width:16px"></i>
                            {{ $appointment->appointment_date->format('M d, Y') }}
                        </div>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="fas fa-clock me-2" style="width:16px"></i>
                            {{ $appointment->appointment_time }}
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-info btn-sm flex-grow-1" data-bs-toggle="modal"
                            data-bs-target="#viewAppointmentModal{{ $appointment->id }}">
                            View Details
                        </button>
                        @if($appointment->status === 'pending')
                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}"
                                class="d-inline flex-grow-1">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-outline-success btn-sm w-100">Approve</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <p>No appointments found.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($appointments->count() > 0)
        <div id="appointmentsPaginationContainer"></div>
    @endif

    <!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.appointment.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Patient Search Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">🔍 Search Existing Patient</label>
                            <input type="text" class="form-control" id="appointmentPatientSearch"
                                placeholder="Search by name, email, or phone..." autocomplete="off">
                            <div id="appointmentSearchResults" class="list-group mt-2" style="display: none;"></div>
                            <div id="appointmentSelectedPatientInfo" class="alert alert-info mt-2" style="display: none;">
                                <strong>Selected Patient:</strong>
                                <div id="appointmentSelectedPatientDetails"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                                    onclick="clearAppointmentPatientSelection()">
                                    <i class="fas fa-times"></i> Clear Selection
                                </button>
                            </div>
                        </div>

                        <div class="text-center my-3">
                            <span class="badge bg-secondary">OR</span>
                        </div>

                        <input type="hidden" name="user_id" id="selected_patient_id">

                        <div id="manualEntrySection">
                            <label class="form-label fw-bold">✚ Create New Appointment (Manual Entry)</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="patient_name" class="form-label">Patient Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="patient_name" name="patient_name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="patient_phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="patient_phone" name="patient_phone">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="patient_address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="patient_address" name="patient_address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_type" class="form-label">Service Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="service_type" name="service_type" required>
                                            <option value="" disabled selected>Select Service</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service }}">{{ $service }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Appointment Date & Time <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="prevMonth">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <h6 class="mb-0" id="currentMonth">Loading...</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="nextMonth">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div id="calendarGrid" class="calendar-grid">
                                        <div class="col-12">
                                            <div class="skeleton calendar-skeleton"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="mb-0">Time Slots</h6>
                                        </div>
                                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                            <div id="selectedDateDisplay" class="mb-3 text-muted">Select a date to view
                                                available time slots</div>
                                            <div id="timeSlotsGrid" class="time-slots-grid">
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                                    <p>Select a date to view time slots</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Hidden inputs to store selected date and time -->
                            <input type="hidden" id="appointment_date" name="appointment_date" required>
                            <input type="hidden" id="appointment_time" name="appointment_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Block Date Modal -->
    <div class="modal fade" id="blockDateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Block Date (Doctor Unavailable)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.appointment.block') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> Blocking this date will:
                            <ul class="mb-0 mt-1">
                                <li>Prevent any new bookings for this date.</li>
                                <li>Mark existing appointments as "Needs Reschedule".</li>
                                <li>Automatically notify affected patients via email.</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date to Block</label>
                            <input type="date" class="form-control" name="date" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason (Optional)</label>
                            <input type="text" class="form-control" name="reason" placeholder="e.g. Doctor on Leave, Maintenance">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Block</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reschedule Appointment Modal (Single Instance) -->
    <div class="modal fade" id="rescheduleAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reschedule Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rescheduleForm" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="rescheduled">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label">New Appointment Date & Time <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="reschedPrevMonth">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <h6 class="mb-0" id="reschedCurrentMonth">Loading...</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="reschedNextMonth">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div id="reschedCalendarGrid" class="calendar-grid">
                                        <div class="col-12">
                                            <div class="skeleton calendar-skeleton"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="mb-0">Time Slots</h6>
                                        </div>
                                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                            <div id="reschedSelectedDateDisplay" class="mb-3 text-muted">Select a date to
                                                view available time slots</div>
                                            <div id="reschedTimeSlotsGrid" class="time-slots-grid">
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                                    <p>Select a date to view time slots</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Hidden inputs to store selected date and time -->
                            <input type="hidden" id="resched_new_date" name="new_date" required>
                            <input type="hidden" id="resched_new_time" name="new_time" required>
                        </div>


                        <div class="mt-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- View Appointment Details Modals -->
        @foreach($appointments as $appointment)
            <!-- View Modal -->
            <div class="modal fade" id="viewAppointmentModal{{ $appointment->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Appointment Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <!-- Left Column: Patient Info -->
                                <div class="col-md-6">
                                    <div class="info-card p-3 rounded-3 h-100">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3 d-flex align-items-center">
                                            <i class="fas fa-user-circle me-2"></i>Patient Information
                                        </h6>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Name</label>
                                            <div class="info-value">{{ $appointment->patient_name }}</div>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Contact</label>
                                            <div class="info-value">
                                                <div><i class="fas fa-phone me-2 text-muted"></i>{{ $appointment->patient_phone }}</div>
                                                <div class="mt-1"><i class="fas fa-envelope me-2 text-muted"></i>{{ $appointment->user->email ?? 'No email linked' }}</div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <label class="info-label">Address</label>
                                            <div class="info-value"><i class="fas fa-map-marker-alt me-2 text-muted"></i>{{ $appointment->patient_address ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Appointment Info -->
                                <div class="col-md-6">
                                    <div class="info-card p-3 rounded-3 h-100">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3 d-flex align-items-center">
                                            <i class="fas fa-calendar-check me-2"></i>Appointment Information
                                        </h6>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Service</label>
                                            <div class="info-value text-primary fw-bold">
                                                <i class="fas fa-stethoscope me-2"></i>{{ $appointment->service_type }}
                                            </div>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Date & Time</label>
                                            <div class="info-value">
                                                <div class="mb-1">
                                                    <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                                    {{ $appointment->appointment_date->format('F d, Y') }}
                                                </div>
                                                <div>
                                                    <i class="fas fa-clock me-2 text-muted"></i>
                                                    {{ $appointment->appointment_time }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="info-label">Status</label>
                                            @php
                                                $statusDisplay = [
                                                    'pending' => 'Pending',
                                                    'approved' => 'Confirmed',
                                                    'rescheduled' => 'Rescheduled',
                                                    'cancelled' => 'Cancelled',
                                                    'completed' => 'Completed',
                                                    'no_show' => 'No Show'
                                                ][$appointment->status] ?? ucfirst($appointment->status);
                                            @endphp
                                            <div>
                                                <span class="status-badge status-{{ $appointment->status }}">{{ $statusDisplay }}</span>
                                            </div>
                                        </div>
                                        @if($appointment->notes)
                                            <div class="info-item">
                                                <label class="info-label">Notes</label>
                                                <div class="notes-box p-2 rounded">{{ $appointment->notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Timestamps -->
                            <div class="row g-3 mt-3 pt-3 border-top">
                                <div class="col-6">
                                    <div class="timestamp-item">
                                        <i class="fas fa-plus-circle me-2 text-muted"></i>
                                        <span class="timestamp-label">Created:</span>
                                        <span class="timestamp-value">{{ $appointment->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="timestamp-item">
                                        <i class="fas fa-edit me-2 text-muted"></i>
                                        <span class="timestamp-label">Updated:</span>
                                        <span class="timestamp-value">{{ $appointment->updated_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <!-- Reschedule -->
                                @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                                    <button type="button" class="btn-modern btn-modern-warning reschedule-btn"
                                        data-appointment-id="{{ $appointment->id }}"
                                        data-action-url="{{ route('admin.appointment.update', $appointment) }}"
                                        data-bs-dismiss="modal">
                                        <i class="fas fa-calendar-alt me-2"></i>Reschedule
                                    </button>
                                @endif

                                <!-- Mark Completed -->
                                @if($appointment->status === 'approved')
                                    <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}"
                                        class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn-modern btn-modern-success">
                                            <i class="fas fa-check-circle me-2"></i>Complete
                                        </button>
                                    </form>
                                @endif

                                <!-- Mark No-show -->
                                @if($appointment->status === 'approved')
                                    <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}"
                                        class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="no_show">
                                        <button type="submit" class="btn-modern btn-modern-secondary">
                                            <i class="fas fa-user-slash me-2"></i>No-show
                                        </button>
                                    </form>
                                @endif

                                <!-- Approve Button (only show if pending) -->
                                @if($appointment->status === 'pending')
                                    <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}"
                                        class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-modern btn-modern-success">
                                            <i class="fas fa-check me-2"></i>Approve
                                        </button>
                                    </form>
                                @endif

                                <!-- Cancel Button -->
                                @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                                    <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}"
                                        class="d-inline cancel-form" id="cancelForm{{ $appointment->id }}">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="button" class="btn-modern btn-modern-danger"
                                            data-form-id="cancelForm{{ $appointment->id }}"
                                            onclick="confirmCancel(this)">
                                            <i class="fas fa-times me-2"></i>Cancel Appointment
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <button type="button" class="btn-modern btn-modern-outline"
                                data-bs-dismiss="modal">
                                <i class="fas fa-times-circle me-2"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let searchTimeout;
        const searchInput = document.getElementById('appointmentPatientSearch');
        const searchResults = document.getElementById('appointmentSearchResults');
        const selectedPatientInfo = document.getElementById('appointmentSelectedPatientInfo');
        const selectedPatientDetails = document.getElementById('appointmentSelectedPatientDetails');
        const selectedPatientId = document.getElementById('selected_patient_id');
        const manualEntrySection = document.getElementById('manualEntrySection');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('admin.patients.search') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            displaySearchResults(data);
                        })
                        .catch(error => console.error('Search error:', error));
                }, 300);
            });
        }

        function displaySearchResults(patients) {
            if (patients.length === 0) {
                searchResults.innerHTML = '<div class="list-group-item text-muted">No patients found</div>';
                searchResults.style.display = 'block';
                return;
            }

            searchResults.innerHTML = patients.map(patient => `
                <a href="#" class="list-group-item list-group-item-action" onclick="selectPatient(${patient.id}, '${patient.name}', '${patient.phone || ''}', '${patient.email || ''}', ${patient.age || 'null'}, '${patient.address || ''}'); return false;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${patient.name}</strong>
                            <div class="small text-muted">
                                ${patient.phone ? `📞 ${patient.phone}` : ''} 
                                ${patient.email ? `📧 ${patient.email}` : ''}
                                ${patient.age ? `👤 Age: ${patient.age}` : ''}
                            </div>
                        </div>
                        <span class="badge bg-primary">Select</span>
                    </div>
                </a>
            `).join('');

            searchResults.style.display = 'block';
        }

        window.selectPatient = function (id, name, phone, email, age, address) {
            selectedPatientId.value = id;
            selectedPatientDetails.innerHTML = `
                <strong>${name}</strong><br>
                <small>${phone ? `📞 ${phone}` : ''} ${email ? `📧 ${email}` : ''} ${age ? `👤 Age: ${age}` : ''}</small>
            `;
            selectedPatientInfo.style.display = 'block';
            searchResults.style.display = 'none';
            searchInput.value = name;

            // Auto-fill and disable manual entry fields
            const nameInput = document.getElementById('patient_name');
            const phoneInput = document.getElementById('patient_phone');
            const addressInput = document.getElementById('patient_address');

            if (nameInput) { nameInput.value = name; nameInput.readOnly = true; }
            if (phoneInput) { phoneInput.value = phone; phoneInput.readOnly = true; }
            if (addressInput) { addressInput.value = address; addressInput.readOnly = true; }
        };

        window.clearAppointmentPatientSelection = function () {
            selectedPatientId.value = '';
            selectedPatientInfo.style.display = 'none';
            searchInput.value = '';
            searchResults.style.display = 'none';

            // Clear and re-enable manual entry fields
            const nameInput = document.getElementById('patient_name');
            const phoneInput = document.getElementById('patient_phone');
            const addressInput = document.getElementById('patient_address');

            if (nameInput) { nameInput.value = ''; nameInput.readOnly = false; }
            if (phoneInput) { phoneInput.value = ''; phoneInput.readOnly = false; }
            if (addressInput) { addressInput.value = ''; addressInput.readOnly = false; }
        };

        // Reset form when modal is closed
        const modal = document.getElementById('addAppointmentModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                const form = modal.querySelector('form');
                if (form) form.reset();
                clearAppointmentPatientSelection();
            });
        }
    });
</script>



@push('scripts')
    <script>
        console.log('Admin calendar script loading...');

        document.addEventListener('DOMContentLoaded', function () {
            console.log('Admin DOM loaded, initializing calendar...');
            const statusFilter = document.getElementById('appointmentStatusFilter');

            // SweetAlert2 Confirmation for Cancel
            window.confirmCancel = function (btn) {
                const formId = btn.getAttribute('data-form-id');
                console.log('confirmCancel called, form ID:', formId);

                if (!formId) {
                    console.error('No form ID found on button');
                    alert('Error: Cannot find form identifier. Please refresh the page.');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to cancel this appointment. This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, cancel it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById(formId);
                        console.log('Form found by ID:', form);

                        if (form) {
                            console.log('Submitting form to:', form.action);
                            form.submit();
                        } else {
                            console.error('Could not find form with ID:', formId);
                            alert('Error: Could not find form to submit. Please refresh the page and try again.');
                        }
                    }
                });
            };
            const serviceFilter = document.getElementById('appointmentServiceFilter');
            const searchInput = document.getElementById('appointmentSearch');
            const resetButton = document.getElementById('appointmentFiltersReset');
            const tableBody = document.getElementById('appointmentsTableBody');
            const summary = document.getElementById('appointmentsFilterSummary');
            const tableRows = tableBody ? Array.from(tableBody.querySelectorAll('tr[data-status]')) : [];

            const normalize = (value) => (value ?? '').toString().trim().toLowerCase();

            const applyAppointmentFilters = () => {
                const statusValue = normalize(statusFilter ? statusFilter.value : '');
                const serviceValue = normalize(serviceFilter ? serviceFilter.value : '');
                const searchValue = normalize(searchInput ? searchInput.value : '');

                let visibleCount = 0;

                tableRows.forEach((row) => {
                    const rowStatus = normalize(row.dataset.status);
                    const rowService = normalize(row.dataset.service);
                    const rowSearchTargets = normalize(
                        [row.dataset.patient, row.dataset.email, row.dataset.phone, row.dataset.service]
                            .filter(Boolean)
                            .join(' ')
                    );

                    let showRow = true;

                    if (statusValue && rowStatus !== statusValue) {
                        showRow = false;
                    }

                    if (serviceValue && rowService !== serviceValue) {
                        showRow = false;
                    }

                    if (searchValue && !rowSearchTargets.includes(searchValue)) {
                        showRow = false;
                    }

                    row.style.display = showRow ? '' : 'none';
                    if (showRow) {
                        visibleCount++;
                    }
                });

                if (summary) {
                    summary.textContent = `Showing ${visibleCount} of ${tableRows.length} appointments`;
                }
            };

            const debounce = (fn, delay = 300) => {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn(...args), delay);
                };
            };

            const debouncedApplyFilters = debounce(applyAppointmentFilters, 250);

            if (statusFilter) {
                statusFilter.addEventListener('change', applyAppointmentFilters);
            }

            if (serviceFilter) {
                serviceFilter.addEventListener('change', applyAppointmentFilters);
            }

            if (searchInput) {
                searchInput.addEventListener('input', debouncedApplyFilters);
            }

            if (resetButton) {
                resetButton.addEventListener('click', () => {
                    if (statusFilter) statusFilter.value = '';
                    if (serviceFilter) serviceFilter.value = '';
                    if (searchInput) searchInput.value = '';
                    applyAppointmentFilters();
                });
            }

            applyAppointmentFilters();

            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-check');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            const bulkApproveBtn = document.getElementById('bulkApprove');
            const bulkCancelBtn = document.getElementById('bulkCancel');
            const bulkCompleteBtn = document.getElementById('bulkComplete');

            // Select All functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActions();
                });
            }

            // Individual checkbox change
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    updateSelectAll();
                    updateBulkActions();
                });
            });

            function updateSelectAll() {
                if (selectAllCheckbox) {
                    const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }
            }

            function updateBulkActions() {
                const checkedBoxes = Array.from(rowCheckboxes).filter(cb => cb.checked);
                const count = checkedBoxes.length;

                if (count > 0) {
                    bulkActions.classList.remove('d-none');
                    selectedCount.textContent = count + ' selected';
                } else {
                    bulkActions.classList.add('d-none');
                }
            }

            // Bulk Approve
            if (bulkApproveBtn) {
                bulkApproveBtn.addEventListener('click', function () {
                    const selectedIds = getSelectedIds();
                    if (selectedIds.length > 0 && confirm('Are you sure you want to approve ' + selectedIds.length + ' appointment(s)?')) {
                        bulkUpdateStatus(selectedIds, 'approved');
                    }
                });
            }

            // Bulk Cancel
            if (bulkCancelBtn) {
                bulkCancelBtn.addEventListener('click', function () {
                    const selectedIds = getSelectedIds();
                    if (selectedIds.length > 0 && confirm('Are you sure you want to cancel ' + selectedIds.length + ' appointment(s)?')) {
                        bulkUpdateStatus(selectedIds, 'cancelled');
                    }
                });
            }

            // Bulk Complete
            if (bulkCompleteBtn) {
                bulkCompleteBtn.addEventListener('click', function () {
                    const selectedIds = getSelectedIds();
                    if (selectedIds.length > 0 && confirm('Are you sure you want to mark ' + selectedIds.length + ' appointment(s) as completed?')) {
                        bulkUpdateStatus(selectedIds, 'completed');
                    }
                });
            }

            function getSelectedIds() {
                return Array.from(rowCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.getAttribute('data-appointment-id'));
            }

            function bulkUpdateStatus(ids, status) {
                // Update appointments one by one using fetch API
                let completed = 0;
                const total = ids.length;

                ids.forEach((id, index) => {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('status', status);

                    fetch(`/admin/appointment/${id}/update`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            completed++;
                            if (completed === total) {
                                // All requests completed, reload the page
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error updating appointment:', error);
                            completed++;
                            if (completed === total) {
                                window.location.reload();
                            }
                        });
                });
            }

            // Restrict date inputs to today up to 1 month from now
            const today = new Date();
            const minDate = today.toISOString().split('T')[0];
            const oneMonthFromNow = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
            const maxDate = oneMonthFromNow.toISOString().split('T')[0];

            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.min = minDate;
                input.max = maxDate;
            });

            // Calendar functionality
            class AppointmentCalendar {
                constructor(config) {
                    console.log('AppointmentCalendar constructor called', config);
                    this.config = config;
                    this.currentDate = new Date();
                    this.selectedDate = null;
                    this.calendarData = [];
                    this.init();
                }

                init() {
                    this.attachEventListeners();
                    this.loadCalendar();
                }

                attachEventListeners() {
                    const prevBtn = document.getElementById(this.config.prevBtnId);
                    const nextBtn = document.getElementById(this.config.nextBtnId);

                    if (prevBtn) {
                        prevBtn.addEventListener('click', () => {
                            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                            this.loadCalendar();
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', () => {
                            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                            this.loadCalendar();
                        });
                    }
                }

                async loadCalendar() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth() + 1;

                    console.log(`Loading calendar for: ${year} ${month}`);

                    try {
                        const response = await fetch(`/admin/appointments/calendar?year=${year}&month=${month}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`Failed to load calendar data: ${response.status} ${response.statusText}`);
                        }

                        const data = await response.json();
                        this.calendarData = data.calendar;
                        this.renderCalendar();
                        this.updateMonthDisplay();
                    } catch (error) {
                        console.error('Error loading calendar:', error);
                        const grid = document.getElementById(this.config.calendarGridId);
                        if (grid) {
                            grid.innerHTML = `<div class="col-12"><div class="skeleton calendar-skeleton"></div></div>`;
                            // After a timeout, show error if it persists or just keep skeleton? 
                            // Better to show error eventually.
                            setTimeout(() => {
                                grid.innerHTML = `<div class="col-12 text-center text-danger">Error loading calendar: ${error.message}</div>`;
                            }, 1000);
                        }
                    }
                }

                renderCalendar() {
                    const calendarGrid = document.getElementById(this.config.calendarGridId);
                    if (!calendarGrid) return;

                    calendarGrid.innerHTML = '';

                    // Add day headers
                    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    dayHeaders.forEach(day => {
                        const header = document.createElement('div');
                        header.className = 'calendar-header';
                        header.textContent = day;
                        calendarGrid.appendChild(header);
                    });

                    // Add empty cells for days before month starts
                    const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                    for (let i = 0; i < firstDay; i++) {
                        const emptyDay = document.createElement('div');
                        calendarGrid.appendChild(emptyDay);
                    }

                    // Add calendar days
                    this.calendarData.forEach(dayData => {
                        const dayElement = document.createElement('div');
                        dayElement.className = 'calendar-day';

                        dayElement.dataset.date = dayData.date; // Added data-date attribute

                        // Create a span for the day number
                        const dayNumber = document.createElement('span');
                        dayNumber.className = 'day-number';
                        dayNumber.textContent = dayData.day;
                        dayElement.appendChild(dayNumber);

                        if (dayData.is_weekend) {
                            dayElement.classList.add('weekend');
                            // Visually disable weekends
                            dayElement.style.opacity = '0.5';
                            dayElement.style.cursor = 'not-allowed';
                            dayElement.style.backgroundColor = '#f8f9fa';
                            dayElement.style.pointerEvents = 'none';
                        }

                        if (dayData.is_past) {
                            dayElement.classList.add('past');
                        } else if (dayData.is_fully_occupied) {
                            dayElement.classList.add('occupied');
                        } else if (dayData.occupied_slots > 0) {
                            dayElement.classList.add('partially-occupied');
                        }

                        // Add slot indicator at the bottom (Only if occupied > 0)
                        if (dayData.occupied_slots > 0) {
                            const indicator = document.createElement('span');
                            indicator.className = 'slot-indicator';
                            indicator.textContent = `${dayData.occupied_slots}/${dayData.total_slots}`;
                            dayElement.appendChild(indicator);
                        }

                        if (!dayData.is_past && !dayData.is_weekend) {
                            dayElement.addEventListener('click', () => {
                                this.selectDate(dayData.date);
                            });
                        }

                        calendarGrid.appendChild(dayElement);
                    });
                }

                updateMonthDisplay() {
                    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
                    const displayEl = document.getElementById(this.config.currentMonthId);
                    if (displayEl) {
                        displayEl.textContent = `${monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`;
                    }
                }

                async selectDate(date) {
                    // Remove previous selection in this calendar
                    const calendarGrid = document.getElementById(this.config.calendarGridId);
                    if (calendarGrid) {
                        calendarGrid.querySelectorAll('.calendar-day.selected').forEach(el => {
                            el.classList.remove('selected');
                        });

                        // Add selection to clicked date
                        calendarGrid.querySelectorAll('.calendar-day').forEach(el => {
                            // Find the day element that matches the date (we didn't store date in dataset in render, let's fix that logic or rely on closure)
                            // Actually, in renderCalendar we added click listener to the specific element. 
                            // But to highlight, we need to find it again or just add class in the click handler?
                            // The click handler calls this.selectDate(dayData.date).
                            // Let's match by text content or better, add data-date in render.
                            // For now, let's assume we add data-date in render or just iterate.
                            // Wait, I didn't add data-date in renderCalendar above. Let's just rely on the fact that we can't easily find it without data attribute.
                            // I will add data-date to the element in renderCalendar in the next iteration or just fix it here.
                            // Actually, let's fix renderCalendar to add data-date.
                        });
                    }

                    // Re-implementing selection logic to be robust
                    const allDays = document.getElementById(this.config.calendarGridId)?.querySelectorAll('.calendar-day') || [];
                    allDays.forEach(el => {
                        // We need a way to identify the date. 
                        // Let's assume I will add data-date in renderCalendar.
                        if (el.dataset.date === date) {
                            el.classList.add('selected');
                        }
                    });

                    this.selectedDate = date;

                    // Update display
                    const selectedDateObj = new Date(date);
                    const formattedDate = selectedDateObj.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    const dateDisplay = document.getElementById(this.config.selectedDateDisplayId);
                    if (dateDisplay) {
                        dateDisplay.textContent = formattedDate;
                    }

                    // Load time slots for selected date
                    await this.loadTimeSlots(date);
                }

                async loadTimeSlots(date) {
                    try {
                        const response = await fetch(`/admin/appointments/slots?date=${date}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`Failed to load time slots`);
                        }

                        const data = await response.json();
                        this.renderTimeSlots(data.slots);
                    } catch (error) {
                        console.error('Error loading time slots:', error);
                        const slotsGrid = document.getElementById(this.config.timeSlotsGridId);
                        if (slotsGrid) {
                            slotsGrid.innerHTML = `<div class="col-12"><div class="skeleton" style="height: 100px; width: 100%;"></div></div>`;
                            setTimeout(() => {
                                slotsGrid.innerHTML = `<div class="col-12 text-center text-danger">Error loading time slots: ${error.message}</div>`;
                            }, 1000);
                        }
                    }
                }

                renderTimeSlots(slots) {
                    const timeSlotsGrid = document.getElementById(this.config.timeSlotsGridId);
                    if (!timeSlotsGrid) return;

                    timeSlotsGrid.innerHTML = '';

                    if (!slots || slots.length === 0) {
                        timeSlotsGrid.innerHTML = '<div class="text-center text-muted">No time slots available</div>';
                        return;
                    }

                    slots.forEach(slot => {
                        const slotElement = document.createElement('div');
                        
                        // Determine slot class and status text based on availability
                        let slotClass = 'time-slot';
                        let statusText = '';
                        
                        if (slot.is_past) {
                            // Past slots are unavailable (gray)
                            slotClass += ' past';
                            statusText = 'Unavailable';
                        } else if (slot.available) {
                            // Available slots (green)
                            slotClass += ' available';
                            statusText = 'Available';
                        } else {
                            // Occupied slots (red) - show count
                            slotClass += ' occupied';
                            statusText = `Occupied (${slot.occupied_count})`;
                        }
                        
                        slotElement.className = slotClass;

                        if (slot.available && !slot.is_past) {
                            slotElement.addEventListener('click', () => {
                                this.selectTimeSlot(slot.time, slot.display);
                            });
                        }

                        const timeElement = document.createElement('div');
                        timeElement.className = 'time';
                        timeElement.textContent = slot.display;

                        const statusElement = document.createElement('div');
                        statusElement.className = 'status';
                        statusElement.textContent = statusText;

                        slotElement.appendChild(timeElement);
                        slotElement.appendChild(statusElement);

                        timeSlotsGrid.appendChild(slotElement);
                    });
                }

                selectTimeSlot(time, display) {
                    const timeSlotsGrid = document.getElementById(this.config.timeSlotsGridId);
                    if (timeSlotsGrid) {
                        timeSlotsGrid.querySelectorAll('.time-slot.selected').forEach(el => {
                            el.classList.remove('selected');
                        });

                        timeSlotsGrid.querySelectorAll('.time-slot').forEach(el => {
                            const timeEl = el.querySelector('.time');
                            if (timeEl && timeEl.textContent === display) {
                                el.classList.add('selected');
                            }
                        });
                    }

                    this.selectedTime = time;

                    // Update hidden input
                    const timeInput = document.getElementById(this.config.timeInputId);
                    const dateInput = document.getElementById(this.config.dateInputId);

                    if (timeInput) timeInput.value = time;
                    if (dateInput) dateInput.value = this.selectedDate;
                }

                // Helper to fix the missing data-date in renderCalendar
                // I'll override the renderCalendar method above to include data-date
            }

            // Initialize Add Appointment Calendar
            const addAppointmentModal = document.getElementById('addAppointmentModal');
            let addCalendar = null;

            if (addAppointmentModal) {
                addAppointmentModal.addEventListener('shown.bs.modal', function () {
                    if (!addCalendar) {
                        addCalendar = new AppointmentCalendar({
                            prevBtnId: 'prevMonth',
                            nextBtnId: 'nextMonth',
                            currentMonthId: 'currentMonth',
                            calendarGridId: 'calendarGrid',
                            selectedDateDisplayId: 'selectedDateDisplay',
                            timeSlotsGridId: 'timeSlotsGrid',
                            dateInputId: 'appointment_date',
                            timeInputId: 'appointment_time'
                        });
                    }
                });

                addAppointmentModal.addEventListener('hidden.bs.modal', function () {
                    if (addCalendar) {
                        addCalendar.selectedDate = null;
                        addCalendar.selectedTime = null;
                        // Reset UI
                        document.getElementById('selectedDateDisplay').textContent = 'Select a date to view available time slots';
                        document.getElementById('timeSlotsGrid').innerHTML = '<div class="text-center text-muted"><i class="fas fa-clock fa-2x mb-2"></i><p>Select a date to view time slots</p></div>';
                        document.getElementById('appointment_date').value = '';
                        document.getElementById('appointment_time').value = '';
                        // Remove selection classes
                        document.querySelectorAll('#calendarGrid .selected').forEach(el => el.classList.remove('selected'));
                    }
                });
            }

            // Initialize Reschedule Appointment Calendar
            const rescheduleModal = document.getElementById('rescheduleAppointmentModal');
            let rescheduleCalendar = null;

            if (rescheduleModal) {
                rescheduleModal.addEventListener('shown.bs.modal', function () {
                    if (!rescheduleCalendar) {
                        rescheduleCalendar = new AppointmentCalendar({
                            prevBtnId: 'reschedPrevMonth',
                            nextBtnId: 'reschedNextMonth',
                            currentMonthId: 'reschedCurrentMonth',
                            calendarGridId: 'reschedCalendarGrid',
                            selectedDateDisplayId: 'reschedSelectedDateDisplay',
                            timeSlotsGridId: 'reschedTimeSlotsGrid',
                            dateInputId: 'resched_new_date',
                            timeInputId: 'resched_new_time'
                        });
                    }
                });

                rescheduleModal.addEventListener('hidden.bs.modal', function () {
                    if (rescheduleCalendar) {
                        rescheduleCalendar.selectedDate = null;
                        rescheduleCalendar.selectedTime = null;
                        // Reset UI
                        document.getElementById('reschedSelectedDateDisplay').textContent = 'Select a date to view available time slots';
                        document.getElementById('reschedTimeSlotsGrid').innerHTML = '<div class="text-center text-muted"><i class="fas fa-clock fa-2x mb-2"></i><p>Select a date to view time slots</p></div>';
                        document.getElementById('resched_new_date').value = '';
                        document.getElementById('resched_new_time').value = '';
                        document.querySelectorAll('#reschedCalendarGrid .selected').forEach(el => el.classList.remove('selected'));
                    }
                });
            }

            // Handle Reschedule Button Clicks
            document.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('reschedule-btn')) {
                    const btn = e.target;
                    const appointmentId = btn.dataset.appointmentId;
                    const actionUrl = btn.dataset.actionUrl;

                    const form = document.getElementById('rescheduleForm');
                    if (form) {
                        form.action = actionUrl;
                    }

                    // Open the modal
                    const modal = new bootstrap.Modal(document.getElementById('rescheduleAppointmentModal'));
                    modal.show();
                }
            });
        });

        // Custom validation for Add Appointment form
        const addAppointmentForm = document.querySelector('#addAppointmentModal form');
        if (addAppointmentForm) {
            addAppointmentForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const selectedPatientId = document.getElementById('selected_patient_id').value;
                const patientNameField = document.getElementById('patient_name');
                const patientName = patientNameField.value.trim();
                const serviceType = document.getElementById('service_type').value;
                const appointmentDate = document.getElementById('appointment_date').value;
                const appointmentTime = document.getElementById('appointment_time').value;

                let missingFields = [];

                // If no patient selected from search, check manual entry (only Patient Name is required)
                if (!selectedPatientId) {
                    // Only check Patient Name if field is not disabled
                    if (!patientNameField.disabled && !patientName) {
                        missingFields.push('Patient Name (or search and select an existing patient)');
                    }
                }

                if (!serviceType) missingFields.push('Service Type');
                if (!appointmentDate) missingFields.push('Appointment Date (select from calendar)');
                if (!appointmentTime) missingFields.push('Time Slot (select from available times)');

                if (missingFields.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        html: '<p class="mb-3">Please fill in the following required fields:</p><ul class="text-start ps-4">' +
                            missingFields.map(field => '<li class="mb-1">' + field + '</li>').join('') +
                            '</ul>',
                        confirmButtonText: 'OK, I\'ll complete them',
                        confirmButtonColor: '#17a2b8',
                        customClass: {
                            popup: 'swal-wide'
                        }
                    });
                    return false;
                }

                // If all validation passes, submit the form
                this.submit();
            });
        }
    </script>
    @if($appointments->count() > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize reusable pagination
            window.paginatorInstance = new TablePaginator({
                tableId: 'appointmentsTable', // This ID is unused in class but good for reference, ensure table has it? Actually class uses bodyId
                tableBodyId: 'appointmentsTableBody',
                paginationContainerId: 'appointmentsPaginationContainer',
                searchId: 'appointmentSearch',
                rowsPerPage: 10,
                filterInputs: {
                    'appointmentStatusFilter': 'data-status',
                    'appointmentServiceFilter': 'data-service'
                }
            });
        });
    </script>
    @endif
@endpush