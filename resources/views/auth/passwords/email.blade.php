@extends('layouts.app')

@push('styles')
<style>
    /* Darker borders for form inputs to match buttons */
    .form-control {
        border-color: #6c757d !important;
    }
    
    .form-control:focus {
        border-color: #495057 !important;
        box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25) !important;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d !important;
    }
</style>
@endpush

@section('content')
@include('partials.home-content')

<div class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
    <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
        <div class="container" style="max-width: 450px;">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-info text-white text-center position-relative py-3">
                    <h4 class="mb-0">Reset Password</h4>
                    <a href="{{ route('login') }}" class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3" aria-label="Close"></a>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                         <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-info w-100 text-white">Send OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
