@section('page-styles')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .booking-card {
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .booking-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .booking-header {
            background: linear-gradient(135deg, #009fb1, #008a9a);
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
        }

        .booking-body {
            padding: 2rem;
        }

        .form-section {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        body.bg-dark .form-section {
            background: #25282c;
            border-color: #2a2f35;
        }

        .form-section h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        body.bg-dark .form-section h6 {
            color: #e6e6e6;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            background: #ffffff;
        }

        body.bg-dark .form-control {
            background: #0f1316;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        .form-control:focus {
            border-color: #009fb1;
            box-shadow: 0 0 0 0.2rem rgba(0, 159, 177, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        body.bg-dark .form-label {
            color: #e6e6e6;
        }

        .btn-primary {
            background: linear-gradient(135deg, #009fb1, #008a9a);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .btn-secondary {
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }

        .is-invalid {
            border-color: #dc3545;
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

        /* Selected state takes priority over partially-occupied */
        .calendar-day.partially-occupied.selected {
            background-color: #009fb1;
            color: white;
            border-color: #009fb1;
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

        .calendar-day .slot-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            font-size: 0.6rem;
            background: rgba(0, 0, 0, 0.1);
            padding: 1px 3px;
            border-radius: 2px;
        }

        .time-slots-container {
            background: #ffffff;
            border: 1px solid #e9ecef !important;
            max-height: 500px;
        }

        body.bg-dark .time-slots-container {
            background: #1e2124 !important;
            border-color: #2a2f35 !important;
        }

        .time-slots-grid-wrapper {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 0.5rem;
        }

        .time-slots-grid-wrapper::-webkit-scrollbar {
            width: 8px;
        }

        .time-slots-grid-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        body.bg-dark .time-slots-grid-wrapper::-webkit-scrollbar-track {
            background: #2a2f35;
        }

        .time-slots-grid-wrapper::-webkit-scrollbar-thumb {
            background: #009fb1;
            border-radius: 4px;
        }

        .time-slots-grid-wrapper::-webkit-scrollbar-thumb:hover {
            background: #008a9a;
        }

        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
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

        /* Dark Mode Form Controls */
        body.bg-dark select.form-control,
        body.bg-dark textarea.form-control {
            background-color: #0f1316;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark select.form-control:focus,
        body.bg-dark textarea.form-control:focus {
            background-color: #161b20;
            border-color: #009fb1;
            color: #e6e6e6;
        }

        body.bg-dark option {
            background-color: #1e2124;
            color: #e6e6e6;
        }

        /* Dark Mode Calendar Navigation & Components */
        body.bg-dark #prevMonth,
        body.bg-dark #nextMonth {
            color: #e6e6e6;
            border-color: #009fb1;
        }

        body.bg-dark #prevMonth:hover,
        body.bg-dark #nextMonth:hover {
            background-color: #009fb1;
            color: #fff;
        }

        body.bg-dark #currentMonth,
        body.bg-dark #selectedDateDisplay {
            color: #e6e6e6;
        }

        /* Dark Mode Time Slots Container */
        body.bg-dark .border.rounded {
            border-color: #2a2f35 !important;
            background-color: #1e2124 !important;
        }

        /* Dark Mode Inner Cards */
        body.bg-dark .card {
            background-color: #1e2124;
            border-color: #2a2f35;
        }

        body.bg-dark .card-header:not(.bg-primary) {
            background-color: #2a2f35;
            border-bottom-color: #343a40;
        }

        body.bg-dark .card-header h6 {
            color: #e6e6e6;
        }

        /* Dark Mode Calendar Grid Refinements */
        body.bg-dark .calendar-day.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        body.bg-dark .calendar-day.occupied {
            background-color: #F53838;
            border-color: #e62929;
            color: #000;
        }

        body.bg-dark .calendar-day.partially-occupied {
            background-color: #D1C700;
            border-color: #bfa500;
            color: #000;
        }

        /* Selected state takes priority in dark mode too */
        body.bg-dark .calendar-day.partially-occupied.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        /* Dark Mode Time Slots */
        body.bg-dark .time-slot.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        /* Step Indicator */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }

        body.bg-dark .step-indicator::before {
            background: #2a2f35;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e9ecef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background: #009fb1;
            border-color: #009fb1;
            color: white;
            box-shadow: 0 0 0 4px rgba(0, 159, 177, 0.2);
        }

        .step.completed .step-circle {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }

        .step-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #009fb1;
            font-weight: 600;
        }

        .step.completed .step-label {
            color: #28a745;
        }

        body.bg-dark .step-circle {
            background: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .step-label {
            color: #b0b0b0;
        }

        /* Calendar Legend */
        .calendar-legend {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        body.bg-dark .calendar-legend {
            background: #25282c;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #495057;
        }

        body.bg-dark .legend-item {
            color: #e6e6e6;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        body.bg-dark .legend-color {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .legend-color.available {
            background: #77dd77;
            border-color: #66cc66;
        }

        body.bg-dark .legend-color.available {
            background: #77dd77;
            border-color: #66cc66;
        }

        .legend-color.partially-occupied {
            background: #D1C700;
        }

        .legend-color.occupied {
            background: #F53838;
        }

        .legend-color.selected {
            background: #009fb1;
        }

        .legend-color.unavailable {
            background: white;
            border-color: #dee2e6;
        }

        body.bg-dark .legend-color.unavailable {
            background: #1e2124;
            border-color: #495057;
        }

        /* Confirmation Modal */
        .modal-content {
            border-radius: 16px;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
        }

        body.bg-dark .modal-content {
            background: #1e2124;
            color: #e6e6e6;
        }

        body.bg-dark .modal-header {
            border-bottom-color: #2a2f35;
        }

        .confirmation-detail {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .confirmation-detail:last-child {
            border-bottom: none;
        }

        body.bg-dark .confirmation-detail {
            border-bottom-color: #2a2f35;
        }

        .confirmation-label {
            font-weight: 600;
            color: #495057;
        }

        body.bg-dark .confirmation-label {
            color: #cbd3da;
        }

        .confirmation-value {
            color: #6c757d;
        }

        body.bg-dark .confirmation-value {
            color: #b0b0b0;
        }

        /* Dark Mode Form Check Labels */
        body.bg-dark .form-check-label {
            color: #e6e6e6;
        }

        /* Dark Mode Text Utilities */
        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }

        body.bg-dark .text-muted.small.fw-bold,
        body.bg-dark h6.text-muted.small.fw-bold {
            color: #cbd3da !important;
        }

        /* ========== RESPONSIVE STYLES ========== */
        
        /* Tablet and below (≤991px) */
        @media (max-width: 991px) {
            .col-md-10 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .booking-body {
                padding: 1.5rem;
            }

            .form-section {
                padding: 1rem;
            }
        }

        /* Tablet (≤768px) */
        @media (max-width: 768px) {
            .booking-card {
                border-radius: 8px;
                margin: 0;
            }

            .booking-header {
                padding: 1rem;
                border-radius: 8px 8px 0 0;
            }

            .booking-header h4 {
                font-size: 1.1rem;
            }

            .booking-header p {
                font-size: 0.875rem;
            }

            .booking-body {
                padding: 1rem;
            }

            .form-section {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }

            /* Step indicator adjustments */
            .step-indicator {
                margin-bottom: 1rem;
            }

            .step-circle {
                width: 36px;
                height: 36px;
                margin-bottom: 0.25rem;
            }

            .step-label {
                font-size: 0.75rem;
            }

            /* Calendar adjustments */
            .calendar-grid {
                font-size: 0.75rem;
                gap: 1px;
            }

            .calendar-header {
                padding: 0.4rem;
                font-size: 0.75rem;
            }

            .calendar-day {
                font-size: 0.75rem;
                min-height: 35px;
            }

            .calendar-day .slot-indicator {
                font-size: 0.55rem;
                padding: 0px 2px;
            }

            /* Time slots */
            .time-slots-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .time-slot {
                padding: 0.6rem;
            }

            /* Legend */
            .calendar-legend {
                gap: 0.5rem;
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .legend-item {
                font-size: 0.8rem;
            }

            .legend-color {
                width: 18px;
                height: 18px;
            }

            /* Buttons */
            .btn-primary,
            .btn-secondary {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
            }

            /* Form controls */
            .form-control {
                padding: 0.6rem;
                font-size: 16px; /* Prevents zoom on iOS */
            }

            .form-label {
                font-size: 0.9rem;
            }
        }

        /* Mobile (≤576px) */
        @media (max-width: 576px) {
            .booking-card {
                border-radius: 6px;
            }

            .booking-header {
                padding: 0.75rem;
            }

            .booking-header h4 {
                font-size: 1rem;
            }

            .booking-header p {
                font-size: 0.8rem;
            }

            .booking-header .d-flex {
                gap: 0.5rem;
            }

            .booking-header .fs-3 {
                font-size: 1.25rem !important;
            }

            .booking-body {
                padding: 0.75rem;
            }

            .form-section {
                padding: 0.6rem;
                margin-bottom: 0.75rem;
            }

            .form-section h6 {
                font-size: 0.9rem;
                margin-bottom: 0.75rem;
            }

            /* Step indicator - more compact */
            .step-indicator {
                margin-bottom: 0.75rem;
            }

            .step-circle {
                width: 32px;
                height: 32px;
                font-size: 0.875rem;
            }

            .step-label {
                font-size: 0.7rem;
            }

            /* Calendar - very compact */
            .calendar-grid {
                font-size: 0.7rem;
                gap: 1px;
            }

            .calendar-header {
                padding: 0.3rem 0.2rem;
                font-size: 0.7rem;
            }

            .calendar-day {
                font-size: 0.7rem;
                min-height: 32px;
                padding: 2px;
            }

            .calendar-day .slot-indicator {
                font-size: 0.5rem;
                padding: 0px 1px;
                bottom: 1px;
                right: 1px;
            }

            /* Calendar navigation */
            #prevMonth,
            #nextMonth {
                padding: 0.4rem 0.6rem;
                font-size: 0.875rem;
            }

            #currentMonth {
                font-size: 0.9rem;
            }

            /* Time slots container */
            .time-slots-container {
                max-height: 400px;
            }

            .time-slots-grid-wrapper {
                max-height: 350px;
            }

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

            /* Legend - stack vertically if needed */
            .calendar-legend {
                gap: 0.4rem;
                padding: 0.6rem;
            }

            .legend-item {
                font-size: 0.75rem;
                gap: 0.4rem;
            }

            .legend-color {
                width: 16px;
                height: 16px;
            }

            /* Buttons - full width on mobile */
            .btn-primary,
            .btn-secondary {
                padding: 0.55rem 1rem;
                font-size: 0.875rem;
            }

            .d-flex.gap-3.justify-content-end {
                flex-direction: column-reverse;
                width: 100%;
            }

            .d-flex.gap-3.justify-content-end .btn {
                width: 100%;
            }

            /* Form controls */
            .form-control,
            .form-select {
                padding: 0.55rem;
                font-size: 16px;
            }

            .form-label {
                font-size: 0.85rem;
            }

            /* Modal adjustments */
            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-header {
                padding: 1rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .confirmation-detail {
                flex-direction: column;
                padding: 0.5rem 0;
                gap: 0.25rem;
            }

            .confirmation-label {
                font-size: 0.85rem;
            }

            .confirmation-value {
                font-size: 0.85rem;
            }
        }

        /* Ensure no horizontal scrolling */
        @media (max-width: 991px) {
            .row {
                margin-left: 0;
                margin-right: 0;
            }

            .row > * {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            /* Prevent content overflow */
            * {
                max-width: 100%;
            }

            .container,
            .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .time-slot,
            .calendar-day,
            .btn {
                min-height: 44px;
                min-width: 44px;
            }

            /* Larger touch targets for calendar */
            .calendar-day {
                min-height: 44px;
            }
        }
    </style>
@endsection
