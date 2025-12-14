@extends('layouts.admin')

@section('title', 'Appointment Details - Patient Portal')
@section('page-title', 'Appointment Details')
@section('page-description', 'Appointment #' . $appointment->id)

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
    {{ substr(\App\Helpers\AuthHelper::user()->name, 0, 2) }}
@endsection

@section('user-name')
    {{ \App\Helpers\AuthHelper::user()->name }}
@endsection

@section('user-role')
    Patient
@endsection

@include('patient.partials.appointment-details-styles')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card appointment-details-card">
                <div class="appointment-details-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">Appointment Details</h4>
                            <p class="text-muted mb-0">Appointment #{{ $appointment->id }}</p>
                        </div>
                        <span class="status-badge 
                                        @if($appointment->status == 'pending') bg-warning text-dark
                                        @elseif($appointment->status == 'approved') bg-success text-white
                                        @elseif($appointment->status == 'completed') bg-info text-white
                                        @elseif($appointment->status == 'cancelled') bg-danger text-white
                                        @else bg-secondary text-white
                                        @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                </div>
@include('patient.partials.appointment-details-info')
            </div>
        </div>
    </div>

@include('patient.partials.appointments-cancel-modal')
@endsection

@push('scripts')
@include('patient.partials.appointment-details-scripts')
@endpush