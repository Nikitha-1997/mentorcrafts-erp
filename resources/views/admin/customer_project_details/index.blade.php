@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Customer Project Details</h4>
        <a href="{{ route('customer-project-details.create') }}" class="btn btn-primary btn-sm">
            Add New
        </a>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th>Customer</th>
                    <th>Project</th>
                    <th>Domain Type</th>
                    <th>Hosting Type</th>
                    <th>Domain Expiry</th>
                    <th>Hosting Expiry</th>
                   <!-- <th>AMC</th>-->
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($details as $detail)
                    <tr>
                        <td>{{ $detail->customer->company_name ?? '-' }}</td>
                        <td>{{ $detail->project_name ?? '-' }}</td>
                        <td>{{ $detail->domain_type ?? '-' }}</td>
                        <td>{{ $detail->hosting_type ?? '-' }}</td>

                        {{-- Domain Expiry --}}
                        <td class="text-center">
                            @if($detail->domain_expiry_date)
                                <span class="{{ \Carbon\Carbon::parse($detail->domain_expiry_date)->isPast() ? 'text-danger fw-semibold' : '' }}">
                                    {{ \Carbon\Carbon::parse($detail->domain_expiry_date)->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- Hosting Expiry --}}
                        <td class="text-center">
                            @if($detail->hosting_expiry_date)
                                <span class="{{ \Carbon\Carbon::parse($detail->hosting_expiry_date)->isPast() ? 'text-danger fw-semibold' : '' }}">
                                    {{ \Carbon\Carbon::parse($detail->hosting_expiry_date)->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- AMC Status --}}
                    <!--    <td class="text-center">
                            @if($detail->amc_description)
                                <span class="badge bg-success">{{ $detail->amc_description }}</span>
                            @elseif($detail->domain_not_included_in_amc || $detail->hosting_not_included_in_amc)
                                <span class="badge bg-warning text-dark">Not in AMC</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>-->

                        {{-- Action Buttons --}}
                        <td class="text-center">
                            <a href="{{ route('customer-project-details.edit', $detail->id) }}" 
                               class="btn btn-sm btn-warning">
                               Edit
                            </a>

                            <form action="{{ route('customer-project-details.destroy', $detail->id) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this record?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No customer project details found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
