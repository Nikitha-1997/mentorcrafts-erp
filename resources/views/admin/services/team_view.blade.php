@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Services Sidebar -->
        <div class="col-md-3 border-end">
            <h5 class="mb-3">Services</h5>
            <ul class="list-group">
                @foreach($services as $service)
                    <li class="list-group-item">
                        <strong>{{ $service->name }}</strong>
                        <ul class="list-unstyled ms-3 mt-2">
                            @forelse($service->customers as $customer)
                                <li>
                                    <a href="{{ route('services.customer.show', [$service->id, $customer->id]) }}">
                                        {{ $customer->company_name }}
                                    </a>
                                </li>
                            @empty
                                <li class="text-muted">No clients assigned</li>
                            @endforelse
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Content Area -->
        <div class="col-md-9 text-center mt-4">
            <!--<h4>Select a client to view their profile</h4>-->
        </div>
    </div>
</div>
@endsection
