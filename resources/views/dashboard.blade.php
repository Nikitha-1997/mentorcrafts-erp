@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4>Welcome, {{ Auth::user()->name ?? 'User' }}</h4>
                <p class="text-muted"></p>
            </div>
        </div>
    </div>
</div>
@endsection
