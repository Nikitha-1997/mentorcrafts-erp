@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">Add New Lead</h4>

    <form action="{{ route('leads.store') }}" method="POST">
        @csrf
        <div class="row g-3">

            {{-- 🏢 Company & Contact Card --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-building me-2"></i>Company & Contact Info
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
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
                            <input type="text" name="address_line1" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address_line2" class="form-control">
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
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control" required>
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
                            <label class="form-label">Services</label>
                            <select name="services[]" id="services" multiple class="form-select" required>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple.</small>
                        </div>

                        <div id="service-costs-section"></div>
                        <h6 class="text-end">Total Cost: <span id="total-cost">₹0.00</span></h6>


                        @php
                            $leadStatuses = array_keys(setting('Lead', 'Statuses', []));
                            $leadStatuses = array_filter($leadStatuses, fn($s) => strtolower($s) !== 'converted');
                        @endphp
                        <div class="mb-3 mt-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Select Status --</option>
                                @foreach($leadStatuses as $status)
                                    <option value="{{ $status }}" {{ $status === 'New' ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lead Source</label>
                            <select name="source" id="source" class="form-select">
                                <option value="">-- Select Source --</option>
                                @foreach(setting('Lead', 'Sources', []) as $source => $subValues)
                                    <option value="{{ $source }}">{{ ucfirst(str_replace('_', ' ', $source)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sub Source</label>
                            <select name="source_sub" id="source_sub" class="form-select">
                                <option value="">-- Select Sub Source --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 📅 Follow-up Card --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-calendar-event me-2"></i>Follow-up Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Follow-up Notes</label>
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
                <button type="submit" class="btn btn-success px-4">Save Lead</button>
                <a href="{{ route('leads.index') }}" class="btn btn-secondary px-4">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- 🧠 Scripts --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Sub Source Dropdown ---
    const leadSources = <?php echo json_encode(setting('Lead', 'Sources', [])); ?>;
    const sourceSelect = document.getElementById('source');
    const subSelect = document.getElementById('source_sub');

    if (sourceSelect && subSelect) {
        sourceSelect.addEventListener('change', function() {
            subSelect.innerHTML = '<option value="">-- Select Sub Source --</option>';
            const selected = this.value;
            if (selected && leadSources[selected]) {
                leadSources[selected].forEach(function(sub) {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.textContent = sub;
                    subSelect.appendChild(option);
                });
            }
        });
    }

    // --- Country, State, District ---
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    const districtSelect = document.getElementById('district');

    fetch('https://countriesnow.space/api/v0.1/countries/positions')
        .then(res => res.json())
        .then(data => {
            data.data.forEach(country => {
                const option = document.createElement('option');
                option.value = country.name;
                option.textContent = country.name;
                countrySelect.appendChild(option);
            });
        });

    countrySelect.addEventListener('change', function() {
        const country = this.value;
        stateSelect.innerHTML = '<option value="">Select State</option>';
        districtSelect.innerHTML = '<option value="">Select District</option>';

        fetch('https://countriesnow.space/api/v0.1/countries/states', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ country })
        })
        .then(res => res.json())
        .then(data => {
            if (data.data?.states) {
                data.data.states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.name;
                    option.textContent = state.name;
                    stateSelect.appendChild(option);
                });
            }
        });
    });

    stateSelect.addEventListener('change', function() {
        const country = countrySelect.value;
        const state = this.value;
        districtSelect.innerHTML = '<option value="">Select District</option>';

        fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ country, state })
        })
        .then(res => res.json())
        .then(data => {
            if (data.data) {
                data.data.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    districtSelect.appendChild(option);
                });
            }
        });
    });

    // --- Service Cost Section ---
const serviceSelect = document.querySelector('select[name="services[]"]');
const serviceCostContainer = document.getElementById('service-costs-section');
const totalCostElement = document.getElementById('total-cost');

serviceSelect.addEventListener('change', function () {
    serviceCostContainer.innerHTML = '';
    totalCostElement.textContent = '₹0.00';
    const selectedServices = Array.from(serviceSelect.selectedOptions).map(opt => opt.value);

    if (selectedServices.length === 0) return;

    selectedServices.forEach(serviceId => {
        fetch(`/services/${serviceId}/costs`)
            .then(res => res.json())
            .then(costs => {
                const serviceName = serviceSelect.querySelector(`option[value="${serviceId}"]`).text;
                const card = document.createElement('div');
                card.classList.add('card', 'mb-3');
                card.setAttribute('data-service', serviceId);

                // Build cost rows
                const costRows = (costs.length > 0 ? costs : [{ name: '', amount: '', billing_type: 'one_time' }])
                    .map(cost => getCostRowHTML(serviceId, cost))
                    .join('');

                card.innerHTML = `
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <strong>${serviceName}</strong>
                        <button type="button" class="btn btn-danger btn-sm deselect-service" data-service="${serviceId}">
                            ❌ Remove Service
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
});

// Helper: Create a new cost row
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

// Helper: Update total cost
function updateTotal() {
    const inputs = document.querySelectorAll('.cost-input');
    let total = 0;
    inputs.forEach(input => total += parseFloat(input.value || 0));
    totalCostElement.textContent = '₹' + total.toFixed(2);
}

// Event delegation for dynamic elements
document.addEventListener('click', e => {
    // ➕ Add cost
    if (e.target.classList.contains('add-cost')) {
        const serviceId = e.target.dataset.service;
        const container = document.getElementById(`cost-container-${serviceId}`);
        // Find the Add Cost button's parent wrapper
        const addButtonWrapper = container.querySelector('.text-end.mt-2');

    // Insert the new cost row *before* the Add Cost button section
       addButtonWrapper.insertAdjacentHTML('beforebegin', getCostRowHTML(serviceId));
    }

    // 🗑 Remove cost
    if (e.target.classList.contains('remove-cost')) {
        e.target.closest('.cost-row').remove();
        updateTotal();
    }

    // ❌ Remove service
    if (e.target.classList.contains('deselect-service')) {
        const serviceId = e.target.dataset.service;
        const option = serviceSelect.querySelector(`option[value="${serviceId}"]`);
        if (option) option.selected = false;

        const card = e.target.closest('.card');
        card.style.transition = 'opacity 0.3s ease';
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 300);
        updateTotal();
    }
});

// Update total on cost input change
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
.remove-cost {
    margin-top: 1.8rem;
}
</style>
@endpush
