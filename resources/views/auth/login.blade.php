@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="auth-page-wrapper pt-5">
    <!-- auth page bg -->
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
        <div class="bg-overlay"></div>
    </div>

    <div class="auth-page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card overflow-hidden">
                        <div class="row g-0">
                            <!-- Left Side (Image / Logo Area) -->
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="auth-cover-left h-100 d-flex flex-column align-items-center justify-content-center text-white p-4">
                                    <div>
                                        <h5 class="text-white">Welcome to {{ config('app.name') }}</h5>
                                        <p class="text-white-50">Your trusted ERP system to manage everything efficiently.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side (Login Form) -->
                            <div class="col-lg-6">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="text-center mt-2">
                                        <h5 class="text-primary">Welcome Back!</h5>
                                      <!--  <p class="text-muted">Sign in to continue to {{ config('app.name') }}.</p>-->
                                    </div>

                                    <div class="p-2 mt-4">
                                        <!-- Session Status -->
                                        @if (session('status'))
                                            <div class="alert alert-success mb-4" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf

                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="email"
                                                    name="email"
                                                    value="{{ old('email') }}"
                                                    required autofocus
                                                    autocomplete="username"
                                                    placeholder="Enter email">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <div class="float-end">
                                                    @if (Route::has('password.request'))
                                                        <a href="{{ route('password.request') }}" class="text-muted">Forgot password?</a>
                                                    @endif
                                                </div>
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password"
                                                    name="password"
                                                    required autocomplete="current-password"
                                                    placeholder="Enter password">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                                <label class="form-check-label" for="remember_me">
                                                    Remember me
                                                </label>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-primary w-100" type="submit">Log In</button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <p class="mb-0">Don’t have an account? 
                                            <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-underline">
                                                Sign up
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
                    </div>
                    <!-- end card -->
                </div>
            </div>
        </div>
        <!-- end container -->
    </div>
    <!-- end auth page content -->

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection
