@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <h4>{{ $service->name }}</h4>

    <ul class="nav nav-tabs" id="serviceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients" type="button" role="tab">Clients</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="visit-tab" data-bs-toggle="tab" data-bs-target="#visit" type="button" role="tab">Visit Tracker</button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="serviceTabsContent">
        <div class="tab-pane fade show active" id="clients" role="tabpanel">
            <ul class="list-group">
                @forelse($service->customers as $customer)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                     
                            <a href="{{ route('customer.profile.show', ['customer' => $customer->id, 'service' => $service->id]) }}" class="text-decoration-none">

                            {{ $customer->company_name }}
                        </a>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No clients found.</li>
                @endforelse
            </ul>
        </div>

        <div class="tab-pane fade" id="visit" role="tabpanel">
            <p>Visit tracker content ...</p>
        </div>
    </div>
</div>
@endsection
