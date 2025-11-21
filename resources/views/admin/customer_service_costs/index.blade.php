@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4 fw-semibold">Pending Service Cost Approvals</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Customer</th>
                <th>Service</th>
                <th>Cost Name</th>
                <th>Quoted Amount</th>
                <th>Billing Type</th>
                <th>Approve Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($costs as $cost)
                <tr>
                    <td>{{ $cost->customer->company_name ?? 'N/A' }}</td>
                    <td>{{ $cost->service->name ?? 'N/A' }}</td>
                    <td>{{ $cost->name }}</td>
                    <td>₹{{ number_format($cost->quoted_amount, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_',' ', $cost->billing_type)) }}</td>
                    <td>
                        <form action="{{ route('service-costs.approve', $cost->id) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="number" step="0.01" name="approved_amount"
                                   value="{{ $cost->quoted_amount }}"
                                   class="form-control form-control-sm w-50" required>
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No pending approvals </td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
