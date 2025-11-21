@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Add Customer Project Details</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('customer-project-details.store') }}">
                @csrf

                {{-- Customer and Project --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Project Name</label>
                        <input type="text" name="project_name" class="form-control" placeholder="Enter project name">
                    </div>
                </div>

                <hr class="my-4">

                {{-- 🔒 Other Technical Details --}}
                <h5 class="fw-bold text-primary mb-3">Technical & SSL</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">SSL Provider</label>
                        <input type="text" name="ssl_provider" class="form-control" placeholder="Enter SSL provider">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">CMS / Technology</label>
                        <input type="text" name="cms_or_technology" class="form-control" placeholder="e.g., Laravel, WordPress">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Credentials</label>
                        <input type="text" name="credentials" class="form-control" placeholder="Access credentials info">
                    </div>
                </div>

                <hr class="my-4">

                {{-- 🌐 Domain Details --}}
                <h5 class="fw-bold text-primary mb-3">Domain Details</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Domain Type</label>
                                        <select name="domain_type" class="form-select">
                        <option value="">Select Domain Type</option>
                        @foreach($domainTypes as $key => $value)
                            <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>

                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Domain Provider</label>
                        <input type="text" name="domain_service_provider" class="form-control" placeholder="Enter domain provider">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Domain Purchase Date</label>
                        <input type="date" name="domain_purchase_date" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Domain Expiry Date</label>
                        <input type="date" name="domain_expiry_date" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Domain Subscription Duration (Months)</label>
                        <input type="number" name="domain_subscription_duration" class="form-control" placeholder="e.g., 12">
                    </div>
                </div>

                <hr class="my-4">

                {{-- 🖥️ Hosting Details --}}
                <h5 class="fw-bold text-primary mb-3">Hosting Details</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Hosting Type</label>
                        <select name="hosting_type" class="form-select">
                    <option value="">Select Hosting Type</option>
                        @foreach($hostingTypes as $key => $value)
                            <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>

                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hosting Provider Name</label>
                        <input type="text" name="hosting_service_provider" class="form-control" placeholder="Enter hosting provider">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hosting Purchase Date</label>
                        <input type="date" name="hosting_purchase_date" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hosting Expiry Date</label>
                        <input type="date" name="hosting_expiry_date" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hosting Subscription Duration (Months)</label>
                        <input type="number" name="hosting_subscription_duration" class="form-control" placeholder="e.g., 12">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Server Details</label>
                        <input type="text" name="server_details" class="form-control" placeholder="Server info (e.g. cPanel)">
                    </div>
                </div>

                <hr class="my-4">

                
        {{-- ===========================
              💡 AMC DETAILS SECTION
        ============================ --}}
        <h5 class="mt-4 mb-3 text-primary fw-bold">AMC Details</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">AMC Description</label>
                <input type="text" name="amc_description" class="form-control" value="{{ old('amc_description') }}" placeholder="Describe the AMC coverage">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">AMC Duration (Months)</label>
                <input type="number" name="amc_month" class="form-control" value="{{ old('amc_month') }}" placeholder="e.g., 12">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">AMC Amount (₹)</label>
                <input type="number" name="amc_amount" step="0.01" class="form-control" value="{{ old('amc_amount') }}" placeholder="e.g., 5000">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">AMC Remarks</label>
                <textarea name="amc_remarks" class="form-control" rows="3" placeholder="Any additional AMC details">{{ old('amc_remarks') }}</textarea>
            </div>
        </div>
         <hr class="my-4">

                

                {{-- 🗒️ Notes --}}
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any additional information..."></textarea>
                    </div>
                </div>

                {{-- AMC checkbox --}}
                
            <div class="col-md-12 mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="domain_not_included_in_amc" name="domain_not_included_in_amc"
                        value="1" {{ old('domain_not_included_in_amc') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="domain_not_included_in_amc">
                     Not Included in AMC
                    </label>
                </div>
            </div>
        </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('customer-project-details.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
