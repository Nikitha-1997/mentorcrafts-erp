@extends('layouts.auth')
@section('title', 'Register')

@section('content')
<div class="auth-page-wrapper pt-5">
    <!-- Background -->
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
        <div class="bg-overlay"></div>
    </div>

    <!-- Page Content -->
    <div class="auth-page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card overflow-hidden">
                        <div class="row g-0">
                            <!-- Left Side (Image / Info) -->
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="auth-cover-left h-100 d-flex flex-column align-items-center justify-content-center text-white p-4">
                                    <div>
                                        <h5 class="text-white">Join {{ config('app.name') }}</h5>
                                        <p class="text-white-50">Create your account and start managing everything seamlessly.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side (Form) -->
                            <div class="col-lg-6">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="text-center mt-2">
                                        <h5 class="text-primary">Create Account</h5>
                                        <p class="text-muted">Get your {{ config('app.name') }} account now.</p>
                                    </div>

                                    <div class="p-2 mt-4">
                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf

                                            <!-- Name -->
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Full Name</label>
                                                <input type="text"
                                                       id="name"
                                                       name="name"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       value="{{ old('name') }}"
                                                       required autofocus autocomplete="name"
                                                       placeholder="Enter your name">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Email -->
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email"
                                                       id="email"
                                                       name="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       value="{{ old('email') }}"
                                                       required autocomplete="username"
                                                       placeholder="Enter your email">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Password -->
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password"
                                                       id="password"
                                                       name="password"
                                                       class="form-control @error('password') is-invalid @enderror"
                                                       required autocomplete="new-password"
                                                       placeholder="Enter password">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Confirm Password -->
                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                                <input type="password"
                                                       id="password_confirmation"
                                                       name="password_confirmation"
                                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                                       required autocomplete="new-password"
                                                       placeholder="Confirm password">
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mt-4">
                                                <button type="submit" class="btn btn-primary w-100">Register</button>
                                            </div>

                                            <div class="mt-4 text-center">
                                                <p class="mb-0">
                                                    Already have an account?
                                                    <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">
                                                        Login
                                                    </a>
                                                </p>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
                    </div> <!-- end card -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
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
