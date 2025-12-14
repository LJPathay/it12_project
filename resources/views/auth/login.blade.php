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
    
    /* Darker border for checkbox */
    .form-check-input {
        border-color: #6c757d !important;
    }
    
    .form-check-input:focus {
        border-color: #495057 !important;
        box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25) !important;
    }
</style>
@endpush

@section('content')
@include('partials.home-content')

<div class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
    <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
        <div class="container" style="max-width: 450px;">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center position-relative py-3">
                    <h4 class="mb-0">Login</h4>
                    <a href="{{ route('home') }}" class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3" aria-label="Close"></a>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-decoration-none small">Forgot Password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 text-white">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <small>Don’t have an account? <a href="{{ route('register') }}">Register here</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
