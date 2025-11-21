@extends('layouts.app')

@section('content')
<div class="container mt-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">Customer: {{ $customer->company_name }}</h4>
        <div>
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning me-2">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Main Layout --}}
    <div class="row">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-8 col-md-12">
            {{-- Contact Details Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Contact Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Customer ID:</strong> {{ $customer->customer_code ?? '—' }}</p>
                            <p><strong>Contact Person:</strong> {{ $customer->contact_person ?? '—' }}</p>
                            <p><strong>Phone:</strong> {{ $customer->phone ?? '—' }}</p>
                            <p><strong>Email:</strong> {{ $customer->email ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong><br>
                                {{ $customer->address_line1 ?? '' }}<br>
                                {{ $customer->address_line2 ?? '' }}<br>
                                {{ $customer->city ?? '' }},
                                {{ $customer->district->name ?? '' }}<br>
                                {{ $customer->state->name ?? '' }},
                                {{ $customer->country->name ?? '' }}<br>
                                <strong>Pincode:</strong> {{ $customer->pincode ?? '' }}
                            </p>
                        </div>
                    </div>

                    @if($customer->lead)
                        <p class="mt-3"><strong>Converted from Lead:</strong>
                            <a href="{{ route('leads.show', $customer->lead->id) }}">
                                {{ $customer->lead->company_name }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>

            {{-- Services Availed Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Services Availed</h5>
                    @if($customer->services->isEmpty())
                        <p class="text-muted">No services assigned.</p>
                    @else
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Service Name</th>
                                    <th>Service Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->services as $cs)
                                    <tr>
                                        <td>{{ $cs->name ?? '—' }}</td>
                                        <td>{{ $cs->pivot->service_code ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Recently Requested Services</h5>
                    @if($customer->recentLeads->isEmpty())
                        <p class="text-muted">No recent service requests.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($customer->recentLeads as $lead)
                                <li class="list-group-item">
                                    <small class="text-muted">{{ $lead->created_at->format('d M Y') }}</small><br>
                                    <span>{{ $lead->services->pluck('name')->join(', ') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>


{{-- Customer Projects Card --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Customer Projects</h5>

        @if($customer->projects->isEmpty())
            <p class="text-muted">No projects created for this customer.</p>
        @else
            @foreach($customer->projects as $project)
              <a href="{{ route('projects.manage', $project->id) }}"
                   class="btn btn-outline-primary btn-sm mb-2 w-100 text-start">
                    {{ $project->project_name }}
                </a>
            @endforeach
        @endif
    </div>
</div>









            
        </div>
    </div>
</div>
@endsection
