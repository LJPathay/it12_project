@extends('layouts.admin')

@section('title', 'My Appointments - Patient Portal')
@section('page-title', 'My Appointments')
@section('page-description', 'Manage your healthcare appointments.')

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

@include('patient.partials.appointments-styles')

@section('content')
@include('patient.partials.appointments-filters')

@include('patient.partials.appointments-table')

@include('patient.partials.appointments-cancel-modal')
@endsection

@push('scripts')
@include('patient.partials.appointments-scripts')
@endpush