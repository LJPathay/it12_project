@extends('layouts.admin')

@section('title', 'Malasakit | Book Appointment')
@section('page-title', 'Book Appointment')
@section('page-description', 'Schedule your healthcare visit')

@section('sidebar-menu')
    <a class="nav-link @if(request()->routeIs('patient.dashboard')) active @endif" href="{{ route('patient.dashboard') }}"
        data-tooltip="Dashboard">
        <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
    </a>
    <a class="nav-link @if(request()->routeIs('patient.appointments') || request()->routeIs('patient.appointment.show')) active @endif"
        href="{{ route('patient.appointments') }}" data-tooltip="My Appointments">
        <i class="fas fa-calendar"></i> <span class="sidebar-content">My Appointments</span>
    </a>
    <a class="nav-link @if(request()->routeIs('patient.book-appointment')) active @endif"
        href="{{ route('patient.book-appointment') }}" data-tooltip="Book Appointment">
        <i class="fas fa-plus"></i> <span class="sidebar-content">Book Appointment</span>
    </a>
@endsection

@section('user-initials')
    {{ substr(optional(\App\Helpers\AuthHelper::user())->name ?? 'Gu', 0, 2) }}
@endsection

@section('user-name')
    {{ optional(\App\Helpers\AuthHelper::user())->name ?? 'Guest' }}
@endsection

@section('user-role')
    Patient
@endsection

@include('patient.partials.book-appointment-styles')


@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Step Indicator -->
            <div class="step-indicator mb-4">
                <div class="step active" data-step="1">
                    <div class="step-circle">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="step-label">Patient Info</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="step-label">Date & Time</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div class="step-label">Details</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Confirm</div>
                </div>
            </div>

            <div class="card booking-card">
                <div class="booking-header text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-calendar-plus fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">Book an Appointment</h4>
                            <p class="mb-0 opacity-75">Schedule your healthcare visit</p>
                        </div>
                    </div>
                </div>
                <div class="booking-body">
@include('patient.partials.book-appointment-form')

                </div>
            </div>
        </div>
    </div>

@include('patient.partials.book-appointment-confirmation-modal')
@endsection

@include('patient.partials.book-appointment-scripts')