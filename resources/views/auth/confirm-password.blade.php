@extends('layouts.guest')

@section('content')
<div class="auth-page-wrapper pt-5">
    <div class="auth-page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-primary">Confirm Password</h5>
                                <p class="text-muted">This is a secure area of the application. Please confirm your password before continuing.</p>
                            </div>

                            <div class="p-2 mt-4">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('password.confirm') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input 
                                            type="password" 
                                            class="form-control @error('password') is-invalid @enderror" 
                                            id="password" 
                                            name="password" 
                                            required 
                                            autocomplete="current-password"
                                            placeholder="Enter your password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mt-4 text-end">
                                        <button class="btn btn-success w-100" type="submit">
                                            Confirm
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="mb-0 text-muted">
                            <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">
                                <i class="ri-arrow-left-line align-bottom me-1"></i> Back to Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
