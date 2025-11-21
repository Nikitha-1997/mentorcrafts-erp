@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Edit Customer</h4>
            <a href="{{ route('customers.index') }}" class="btn btn-light btn-sm">← Back</a>
        </div>

        <div class="card-body">
            <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Basic Info --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" 
                               value="{{ old('company_name', $customer->company_name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" 
                               value="{{ old('contact_person', $customer->contact_person) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" 
                               value="{{ old('phone', $customer->phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email', $customer->email) }}">
                    </div>

                    {{-- Address --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" name="address_line1" class="form-control" 
                               value="{{ old('address_line1', $customer->address_line1) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" name="address_line2" class="form-control" 
                               value="{{ old('address_line2', $customer->address_line2) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Country</label>
                        <select name="country" id="country" class="form-select" required>
                            <option value="">Select Country</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">State</label>
                        <select name="state" id="state" class="form-select" required>
                            <option value="">Select State</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">District</label>
                        <select name="district" id="district" class="form-select" required>
                            <option value="">Select District</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" 
                               value="{{ old('city', $customer->city) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" 
                               value="{{ old('pincode', $customer->pincode) }}">
                    </div>

                    {{-- Services --}}
                   <!-- <div class="col-md-12 mb-3">
                        <label class="form-label">Services</label>
                        <select name="services[]" id="services" class="form-select" multiple>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}"
                                    {{ in_array($service->id, old('services', $customer->services->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>-->
                </div>

                {{-- ✅ Service Costs Section --}}
                <h5>Services Availed</h5>
                <div id="service-costs-container">
                    @foreach($customer->services as $service)
                        <div class="card mt-3 service-cost-card" data-service-id="{{ $service->id }}">
                            <div class="card-header bg-light fw-semibold">
                                {{ $service->name }} - Cost Details
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered mb-0 service-cost-table">
                                    <thead>
                                        <tr>
                                            <th>Cost Name</th>
                                            <th>Amount</th>
                                            <th>Billing Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     @foreach($customer->serviceCosts->where('service_id', $service->id) as $cost)
<tr>
    <td>
        <input type="hidden" name="costs[{{ $service->id }}][id][]" value="{{ $cost->id }}">
        <input type="text" name="costs[{{ $service->id }}][name][]" value="{{ $cost->name }}" class="form-control" required>
    </td>
    <td>
        <input type="number" name="costs[{{ $service->id }}][quoted_amount][]" value="{{ $cost->quoted_amount }}" step="0.01" class="form-control" required>
    </td>
    <td>
        <select name="costs[{{ $service->id }}][billing_type][]" class="form-select">
            @foreach(['one_time','monthly','yearly','per_sqft','per_head'] as $type)
                <option value="{{ $type }}" {{ $cost->billing_type === $type ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_',' ', $type)) }}
                </option>
            @endforeach
        </select>
    </td>
    <td><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
</tr>
@endforeach

                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2 add-row">+ Add Row</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success">Update Customer</button>
                </div>
            </form>
            <h5 class="mt-4">Request New Service</h5>
<form action="{{ route('customers.request-service', $customer->id) }}" method="POST">
    @csrf
    <div class="row mb-3">
        <div class="col-md-8">
            <label class="form-label">Select New Services</label>
            <select name="services[]" class="form-select" multiple required>
                @foreach($services as $service)
                    @if(!$customer->services->contains($service->id))
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endif
                @endforeach
            </select>
            <small class="text-muted">Hold Ctrl or Cmd to select multiple</small>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">
                Create Lead for New Services
            </button>
        </div>
    </div>
</form>
        </div>
    </div>
</div>

{{-- 🌎 Address JS --}}
<script>
const selectedCountry = "{{ $customer->country ?? '' }}";
const selectedState = "{{ $customer->state ?? '' }}";
const selectedDistrict = "{{ $customer->district ?? '' }}";

async function loadCountries() {
    const res = await fetch("https://countriesnow.space/api/v0.1/countries/positions");
    const json = await res.json();
    const countries = json.data;

    const countrySelect = document.getElementById('country');
    countries.forEach(c => {
        const option = document.createElement('option');
        option.value = c.name;
        option.textContent = c.name;
        if (c.name === selectedCountry) option.selected = true;
        countrySelect.appendChild(option);
    });

    if (selectedCountry) loadStates(selectedCountry);
}

async function loadStates(country) {
    const res = await fetch("https://countriesnow.space/api/v0.1/countries/states", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ country })
    });
    const json = await res.json();
    const states = json.data.states || [];

    const stateSelect = document.getElementById('state');
    stateSelect.innerHTML = '<option value="">Select State</option>';
    states.forEach(s => {
        const option = document.createElement('option');
        option.value = s.name;
        option.textContent = s.name;
        if (s.name === selectedState) option.selected = true;
        stateSelect.appendChild(option);
    });

    if (selectedState) loadDistricts(selectedState);
}

async function loadDistricts(state) {
    const country = document.getElementById('country').value;
    const districtSelect = document.getElementById('district');

    districtSelect.innerHTML = '<option value="">Select District</option>';

    const res = await fetch("https://countriesnow.space/api/v0.1/countries/state/cities", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ country, state })
    });

    const json = await res.json();
    const districts = json.data || [];

    districts.forEach(d => {
        const option = document.createElement('option');
        option.value = d;
        option.textContent = d;
        if (d === selectedDistrict) option.selected = true;
        districtSelect.appendChild(option);
    });
}

document.getElementById('country').addEventListener('change', e => loadStates(e.target.value));
document.getElementById('state').addEventListener('change', e => loadDistricts(e.target.value));
loadCountries();

// 💰 Add/remove cost rows dynamically
 document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-row')) {
            const table = e.target.closest('.service-cost-card').querySelector('tbody');
            const serviceId = e.target.closest('.service-cost-card').dataset.serviceId;
            const row = `
                <tr>
                    <td><input type="text" name="costs[${serviceId}][name][]" class="form-control" required></td>
                    <td><input type="number" step="0.01" name="costs[${serviceId}][quoted_amount][]" class="form-control" required></td>
                    <td>
                        <select name="costs[${serviceId}][billing_type][]" class="form-select">
                            <option value="one_time">One Time</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="per_sqft">Per Sqft</option>
                            <option value="per_head">Per Head</option>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
                </tr>`;
            table.insertAdjacentHTML('beforeend', row);
        }

        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });

</script>
@endsection
