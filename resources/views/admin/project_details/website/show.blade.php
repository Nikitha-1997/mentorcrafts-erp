@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Customer Project Details</h5>
        </div>

        <div class="card-body">

            {{-- Basic Info --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <strong>Customer:</strong>
                    <p>{{ $detail->customer->company_name }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Project Name:</strong>
                    <p>{{ $detail->project_name }}</p>
                </div>
            </div>

            <hr>

            {{-- Technical Details --}}
            <h5 class="fw-bold text-primary mb-3">Technical & SSL</h5>
            <div class="row mb-4">
                <div class="col-md-4">
                    <strong>SSL Provider:</strong>
                    <p>{{ $detail->ssl_provider ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>CMS / Technology:</strong>
                    <p>{{ $detail->cms_or_technology ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Credentials:</strong>
                    <p>{{ $detail->credentials ?? '—' }}</p>
                </div>
            </div>

            <hr>

            {{-- Domain Details --}}
            <h5 class="fw-bold text-primary mb-3">Domain Details</h5>
            <div class="row mb-4">
                <div class="col-md-4">
                    <strong>Domain Type:</strong>
                    <p>{{ $detail->domain_type ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Domain Provider:</strong>
                    <p>{{ $detail->domain_service_provider ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Purchase Date:</strong>
                    <p>{{ $detail->domain_purchase_date ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Expiry Date:</strong>
                    <p>{{ $detail->domain_expiry_date ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Subscription Duration (Months):</strong>
                    <p>{{ $detail->domain_subscription_duration ?? '—' }}</p>
                </div>
            </div>

            <hr>

            {{-- Hosting Details --}}
            <h5 class="fw-bold text-primary mb-3">Hosting Details</h5>
            <div class="row mb-4">
                <div class="col-md-4">
                    <strong>Hosting Type:</strong>
                    <p>{{ $detail->hosting_type ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Hosting Provider:</strong>
                    <p>{{ $detail->hosting_service_provider ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Purchase Date:</strong>
                    <p>{{ $detail->hosting_purchase_date ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Expiry Date:</strong>
                    <p>{{ $detail->hosting_expiry_date ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Subscription Duration (Months):</strong>
                    <p>{{ $detail->hosting_subscription_duration ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Server Details:</strong>
                    <p>{{ $detail->server_details ?? '—' }}</p>
                </div>
            </div>

            <hr>

            {{-- AMC Details --}}
            <h5 class="fw-bold text-primary mb-3">AMC Details</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <strong>AMC Description:</strong>
                    <p>{{ $detail->amc_description ?? '—' }}</p>
                </div>

                <div class="col-md-3">
                    <strong>AMC Duration (Months):</strong>
                    <p>{{ $detail->amc_month ?? '—' }}</p>
                </div>

                <div class="col-md-3">
                    <strong>AMC Amount (₹):</strong>
                    <p>{{ $detail->amc_amount ?? '—' }}</p>
                </div>

                <div class="col-md-12">
                    <strong>AMC Remarks:</strong>
                    <p>{{ $detail->amc_remarks ?? '—' }}</p>
                </div>

                <div class="col-md-12">
                    <strong>Domain Not Included in AMC:</strong>
                    <p>{{ $detail->domain_not_included_in_amc ? 'Yes' : 'No' }}</p>
                </div>
            </div>

            <hr>

            {{-- Notes --}}
            <h5 class="fw-bold text-primary mb-3">Notes</h5>
            <p>{{ $detail->notes ?? '—' }}</p>

            {{-- Buttons --}}
            <div class="mt-4 d-flex justify-content-end">
                <a href="{{ route('customer-project-details.edit', $detail->id) }}" class="btn btn-warning me-2">
                    Edit
                </a>

                <a href="{{ route('customer-project-details.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
