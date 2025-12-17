@extends('admin.layout')

@section('title', 'Malasakit | Services Management')
@section('page-title', 'Services Management')
@section('page-description', 'Manage health center services')

@section('page-styles')
<style>
    /* Hide Bootstrap pagination's built-in "Showing" text on the left */
    nav[role="navigation"] p,
    nav[role="navigation"] .text-sm {
        display: none !important;
    }
    
    /* Bring showing text closer to pagination */
    #servicesPaginationContainer > div:last-child {
        margin-top: -0.5rem !important;
    }

    /* Center align action button icons */
    .table tbody td .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .table tbody td .btn i {
        margin: 0;
        line-height: 1;
    }
</style>
@endsection

@section('content')
<div class="p-0 p-md-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Service
        </a>
    </div>

    @if($services->count() > 0)
        <x-card :no-padding="true">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Service Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $service->name }}</td>
                            <td>{{ Str::limit($service->description ?? 'N/A', 50) }}</td>
                            <td>
                                <span class="badge bg-{{ $service->active ? 'success' : 'secondary' }}">
                                    {{ $service->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
        
        @if($services->hasPages())
            <div class="d-flex flex-column align-items-center mt-4" id="servicesPaginationContainer">
                <div>
                    {{ $services->links('pagination::bootstrap-5') }}
                </div>
                <div class="small text-muted mb-0 mt-n2">
                    @if($services->total() > 0)
                        Showing {{ $services->firstItem() }}-{{ $services->lastItem() }} of {{ $services->total() }} services
                    @else
                        Showing 0 services
                    @endif
                </div>
            </div>
        @endif
    @else
        <x-card>
            <x-empty-state 
                icon="fa-briefcase-medical" 
                title="No services found" 
                description="Start by adding your first service to the system."
            >
                <x-slot name="action">
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add First Service
                    </a>
                </x-slot>
            </x-empty-state>
        </x-card>
    @endif
</div>
@endsection
