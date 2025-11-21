@extends('layouts.guest')

@section('content')
<div class="auth-page-wrapper pt-5">
    <div class="auth-page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="text-center">
                                <div class="avatar-lg mx-auto mt-2 mb-3">
                                    <div class="avatar-title bg-soft-success text-success display-6 rounded-circle">
                                        <i class="ri-mail-send-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-primary">Verify Your Email</h5>
                                <p class="text-muted">Thanks for signing up! Before getting started, please verify your email by clicking on the link we sent to your inbox.</p>
                            </div>

                            @if (session('status') == 'verification-link-sent')
                                <div class="alert alert-success mt-3">
                                    A new verification link has been sent to your registered email address.
                                </div>
                            @endif

                            <div class="mt-4">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        Resend Verification Email
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-decoration-none text-danger">
                                        <i class="ri-logout-box-line me-1"></i> Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="mb-0 text-m
