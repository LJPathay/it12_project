@extends('layouts.app')

@section('content')
@include('partials.home-content')

<div class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
    <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
        <div class="container" style="max-width: 450px;">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-info text-white text-center position-relative py-3">
                    <h4 class="mb-0">Set New Password</h4>
                    <a href="{{ route('login') }}" class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3" aria-label="Close"></a>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                         <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="otp" value="{{ $otp }}">

                        <div class="mb-3">
                            <label for="reset-password" class="form-label">New Password</label>
                            <input id="reset-password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="reset-password-confirm" class="form-label">Confirm Password</label>
                            <input id="reset-password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-info w-100 text-white">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
