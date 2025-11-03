@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Customer: {{ $customer->company_name }}</h4>
        <div>
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row gx-3">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Contact Details</h5>
                    <p><strong>Customer ID:</strong> {{ $customer->customer_code ?? '—' }}</p>
                    <p><strong>Contact Person:</strong> {{ $customer->contact_person ?? '—' }}</p>
                    <p><strong>Phone:</strong> {{ $customer->phone ?? '—' }}</p>
                    <p><strong>Email:</strong> {{ $customer->email ?? '—' }}</p>
                    <p><strong>Address:</strong><br>
                        {{ $customer->address_line1 ?? '' }}<br>
                        {{ $customer->address_line2 ?? '' }}<br>
                        {{ $customer->city ?? '' }},
                        {{ $customer->district->name ?? '' }}<br>
                        {{ $customer->state->name ?? '' }},
                        {{ $customer->country->name ?? '' }}<br>
                        Pincode: {{ $customer->pincode ?? '' }}
                    </p>

                    @if($customer->lead)
                        <p><strong>Converted from Lead:</strong>
                            <a href="{{ route('leads.show', $customer->lead->id) }}">
                                {{ $customer->lead->company_name }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Services Availed</h5>
                    @if($customer->services->isEmpty())
                        <p class="text-muted">No services assigned.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
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
    </div>
</div>
@endsection
