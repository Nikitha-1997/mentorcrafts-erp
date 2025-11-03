@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Lead</h4>
                    <a href="{{ route('leads.index') }}" class="btn btn-light btn-sm">← Back</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('leads.update', $lead->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            {{-- 🏢 Company & Contact Card --}}
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 rounded-3">
                                    <div class="card-header bg-light fw-semibold">
                                        <i class="bi bi-building me-2"></i>Company & Contact Info
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                            <input type="text" name="company_name" class="form-control"
                                                value="{{ old('company_name', $lead->company_name) }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                                            <input type="text" name="contact_person" class="form-control"
                                                value="{{ old('contact_person', $lead->contact_person) }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Designation</label>
                                            <input type="text" name="designation" class="form-control"
                                                value="{{ old('designation', $lead->designation) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone', $lead->phone) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ old('email', $lead->email) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 📍 Address Card --}}
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 rounded-3">
                                    <div class="card-header bg-light fw-semibold">
                                        <i class="bi bi-geo-alt-fill me-2"></i>Address Details
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Address Line 1</label>
                                            <input type="text" name="address_line1" class="form-control"
                                                value="{{ old('address_line1', $lead->address_line1) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address Line 2</label>
                                            <input type="text" name="address_line2" class="form-control"
                                                value="{{ old('address_line2', $lead->address_line2) }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Country</label>
                                                <select name="country" id="country" class="form-select" required>
                                                    <option value="">Select Country</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">State</label>
                                                <select name="state" id="state" class="form-select" required>
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">District</label>
                                                <select name="district" id="district" class="form-select" required>
                                                    <option value="">Select District</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">City</label>
                                                <input type="text" name="city" class="form-control"
                                                    value="{{ old('city', $lead->city) }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Pincode</label>
                                                <input type="text" name="pincode" class="form-control"
                                                    value="{{ old('pincode', $lead->pincode) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 🧩 Lead Info Card --}}
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 rounded-3">
                                    <div class="card-header bg-light fw-semibold">
                                        <i class="bi bi-info-circle me-2"></i>Lead Details
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Services Availed</label>
                                            <select name="services[]" multiple class="form-select">
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" 
                                                        {{ in_array($service->id, old('services', $lead->services->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="service-costs-section" class="mt-3"></div>
                                            <h6 class="text-end">Total Cost: <span id="total-cost">₹0.00</span></h6>

                                            <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple.</small>
                                        </div>

                                        @php
                                            $leadStatuses = array_keys(setting('Lead', 'Statuses', []));
                                            $leadStatuses = array_filter($leadStatuses, fn($s) => strtolower($s) !== 'converted');
                                        @endphp

                                        <div class="mb-3">
                                            <label class="form-label">Lead Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="">-- Select Status --</option>
                                                @foreach($leadStatuses as $status)
                                                    <option value="{{ $status }}" {{ $lead->status === $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 📅 Follow-up Notes --}}
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 rounded-3">
                                    <div class="card-header bg-light fw-semibold">
                                        <i class="bi bi-calendar-event me-2"></i>Follow-up Notes
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Add Follow-up Notes</label>
                                            <textarea name="notes" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Next Follow-up Date</label>
                                            <input type="datetime-local" name="next_followup_date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-4">Update Lead</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Follow-up History --}}
            <div class="card mt-4 border-0 shadow-sm rounded-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Follow-up History</h5>
                </div>
                <div class="card-body">
                    @if($lead->followups->isEmpty())
                        <p class="text-muted">No follow-up records yet.</p>
                    @else
                        <ul class="list-group">
                            @foreach($lead->followups->sortByDesc('created_at') as $f)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $f->staff?->name ?? 'System' }}</strong>
                                            <small class="text-muted"> — {{ $f->created_at->format('d M Y, H:i') }}</small>
                                            <p class="mb-1">{{ $f->notes }}</p>
                                            @if($f->next_followup_date)
                                                <small class="text-info">Next Follow-up: {{ $f->next_followup_date->format('d M Y, H:i') }}</small>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('followups.destroy', $f->id) }}" onsubmit="return confirm('Delete this follow-up?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="ri-delete-bin-line"></i></button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    const districtSelect = document.getElementById('district');

    const selectedCountry = "{{ old('country', $lead->country) }}";
    const selectedState = "{{ old('state', $lead->state) }}";
    const selectedDistrict = "{{ old('district', $lead->district) }}";

    // Load all countries
    fetch('https://countriesnow.space/api/v0.1/countries/positions')
        .then(res => res.json())
        .then(data => {
            data.data.forEach(country => {
                let option = document.createElement('option');
                option.value = country.name;
                option.textContent = country.name;
                if (country.name === selectedCountry) option.selected = true;
                countrySelect.appendChild(option);
            });
            if (selectedCountry) loadStates(selectedCountry);
        });

    // Fetch states
    function loadStates(country) {
        fetch('https://countriesnow.space/api/v0.1/countries/states', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ country })
        })
        .then(res => res.json())
        .then(data => {
            stateSelect.innerHTML = '<option value="">Select State</option>';
            if (data.data?.states) {
                data.data.states.forEach(state => {
                    let option = document.createElement('option');
                    option.value = state.name;
                    option.textContent = state.name;
                    if (state.name === selectedState) option.selected = true;
                    stateSelect.appendChild(option);
                });
                if (selectedState) loadDistricts(country, selectedState);
            }
        });
    }

    // Fetch districts
    function loadDistricts(country, state) {
        fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ country, state })
        })
        .then(res => res.json())
        .then(data => {
            districtSelect.innerHTML = '<option value="">Select District</option>';
            if (data.data) {
                data.data.forEach(city => {
                    let option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    if (city === selectedDistrict) option.selected = true;
                    districtSelect.appendChild(option);
                });
            }
        });
    }

    countrySelect.addEventListener('change', function() {
        loadStates(this.value);
        districtSelect.innerHTML = '<option value="">Select District</option>';
    });

    stateSelect.addEventListener('change', function() {
        loadDistricts(countrySelect.value, this.value);
    });
     // -------------------- SERVICE COST SECTION --------------------
    const serviceSelect = document.querySelector('select[name="services[]"]');
    const serviceCostContainer = document.getElementById('service-costs-section');
    const totalCostElement = document.getElementById('total-cost');

    // When user selects or unselects services
    serviceSelect.addEventListener('change', loadSelectedServices);

    // Load initially for pre-selected services (edit mode)
    loadSelectedServices();

 function loadSelectedServices() {
    serviceCostContainer.innerHTML = '';
    totalCostElement.textContent = '₹0.00';
    const selectedServices = Array.from(serviceSelect.selectedOptions).map(opt => opt.value);

    if (selectedServices.length === 0) return;

    selectedServices.forEach(serviceId => {
        fetch(`/leads/{{ $lead->id }}/service/${serviceId}/costs`)
            .then(res => res.json())
            .then(costs => {
                const serviceName = serviceSelect.querySelector(`option[value="${serviceId}"]`).text;
                const card = document.createElement('div');
                card.classList.add('card', 'mb-3');
                card.setAttribute('data-service', serviceId);

                const costRows = (costs.length > 0 ? costs : [{ name: '', amount: '', billing_type: 'one_time' }])
                    .map(cost => getCostRowHTML(serviceId, cost))
                    .join('');

                card.innerHTML = `
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <strong>${serviceName}</strong>
                        <button type="button" class="btn btn-danger btn-sm deselect-service" data-service="${serviceId}">
                            🗙 
                        </button>
                    </div>
                    <div class="card-body" id="cost-container-${serviceId}">
                        ${costRows}
                        <div class="text-end mt-2">
                            <button type="button" class="btn btn-light btn-sm add-cost" data-service="${serviceId}">
                                + Add Cost
                            </button>
                        </div>
                    </div>
                `;
                serviceCostContainer.appendChild(card);
                updateTotal();
            });
    });
}

// Create cost row HTML dynamically
function getCostRowHTML(serviceId, cost = {}) {
    return `
        <div class="row mb-2 align-items-center cost-row">
            <div class="col-md-4">
                <input type="text" class="form-control" name="cost_name[${serviceId}][]" value="${cost.name || ''}" placeholder="Cost name">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control cost-input" name="cost_amount[${serviceId}][]" value="${cost.amount || ''}" placeholder="Amount" min="0">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="cost_billing_type[${serviceId}][]">
                    <option value="one_time" ${cost.billing_type === 'one_time' ? 'selected' : ''}>One Time</option>
                    <option value="monthly" ${cost.billing_type === 'monthly' ? 'selected' : ''}>Monthly</option>
                    <option value="yearly" ${cost.billing_type === 'yearly' ? 'selected' : ''}>Yearly</option>
                    <option value="per_sqft" ${cost.billing_type === 'per_sqft' ? 'selected' : ''}>Per Sqft</option>
                    <option value="per_head" ${cost.billing_type === 'per_head' ? 'selected' : ''}>Per Head</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-cost">🗑</button>
            </div>
        </div>
    `;
}

    function updateTotal() {
        const inputs = document.querySelectorAll('.cost-input');
        let total = 0;
        inputs.forEach(input => total += parseFloat(input.value || 0));
        totalCostElement.textContent = '₹' + total.toFixed(2);
    }
    // Event delegation for add/remove cost rows
document.addEventListener('click', e => {
    // ➕ Add new cost row
    if (e.target.classList.contains('add-cost')) {
        const serviceId = e.target.dataset.service;
        const container = document.getElementById(`cost-container-${serviceId}`);
        // Find the Add Cost button's parent wrapper
        const addButtonWrapper = container.querySelector('.text-end.mt-2');

    // Insert the new cost row *before* the Add Cost button section
       addButtonWrapper.insertAdjacentHTML('beforebegin', getCostRowHTML(serviceId));
    }

    // 🗑 Remove cost row
    if (e.target.classList.contains('remove-cost')) {
        const row = e.target.closest('.cost-row');
        row.remove();
        updateTotal();
    }

    // ❌ Deselect service
    if (e.target.classList.contains('deselect-service')) {
        const serviceId = e.target.dataset.service;
        const option = serviceSelect.querySelector(`option[value="${serviceId}"]`);
        if (option) option.selected = false; // deselect from dropdown

        // remove the card visually with a fade animation
        const card = e.target.closest('.card');
        card.style.transition = 'opacity 0.3s ease';
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 300);

        updateTotal();
    }
});



    document.addEventListener('input', e => {
        if (e.target.classList.contains('cost-input')) updateTotal();
    });
});
</script>
@endpush

@push('styles')
<style>
.card-header {
    background-color: #42bbf7ff !important;
    border-bottom: 1px solid #dee2e6;
}
.card {
    transition: all 0.2s ease-in-out;
}
.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.cost-row {
    transition: background-color 0.2s;
}
.cost-row:hover {
    background-color: #f8f9fa;
}
.add-cost {
    border-radius: 20px;
}
.remove-cost {
    border-radius: 50%;
    padding: 3px 8px;
}
.deselect-service {
    border-radius: 20px;
}
</style>
@endpush
