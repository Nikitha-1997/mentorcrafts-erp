@extends('layouts.auth')
@section('title', 'Forgot Password')

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
                            <!-- Left Side (Image/Info) -->
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="auth-cover-left h-100 d-flex flex-column align-items-center justify-content-center text-white p-4">
                                    <div>
                                        <h5 class="text-white">Reset Your Password</h5>
                                        <p class="text-white-50">Enter your registered email address to receive a password reset link.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side (Form) -->
                            <div class="col-lg-6">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="text-center mt-2">
                                        <h5 class="text-primary">Forgot Password?</h5>
                                        <p class="text-muted">We'll send you a reset link to your registered email.</p>
                                    </div>

                                    <div class="p-2 mt-4">
                                        <!-- Session Status -->
                                        @if (session('status'))
                                            <div class="alert alert-success mb-4" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('password.email') }}">
                                            @csrf

                                            <!-- Email Address -->
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email"
                                                       id="email"
                                                       name="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       value="{{ old('email') }}"
                                                       required autofocus
                                                       placeholder="Enter your email">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mt-4">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Send Password Reset Link
                                                </button>
                                            </div>

                                            <div class="mt-4 text-center">
                                                <p class="mb-0">
                                                    Remember your password?
                                                    <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">
                                                        Login
                                                    </a>
                                                </p>
                                            </div>
                                        </form>
                                    </div
