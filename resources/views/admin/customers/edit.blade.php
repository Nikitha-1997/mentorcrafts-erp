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

                    <!-- Address Section -->
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

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Services</label>
                        <select name="services[]" class="form-select" multiple>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}"
                                    {{ in_array($service->id, old('services', $customer->services->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success">Update Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 🌎 Dynamic Address Script using public API --}}
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

/*async function loadDistricts(state) {
    const res = await fetch("https://api.countrystatecity.in/v1/countries/IN/states", {
        headers: {
            "X-CSCAPI-KEY": "YOUR_API_KEY" // optional; only if using that API
        }
    });
    const districts = []; // Replace with actual district API if available

    const districtSelect = document.getElementById('district');
    districtSelect.innerHTML = '<option value="">Select District</option>';
    districts.forEach(d => {
        const option = document.createElement('option');
        option.value = d.name;
        option.textContent = d.name;
        if (d.name === selectedDistrict) option.selected = true;
        districtSelect.appendChild(option);
    });
}*/
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


document.getElementById('country').addEventListener('change', e => {
    loadStates(e.target.value);
});

document.getElementById('state').addEventListener('change', e => {
    loadDistricts(e.target.value);
});

loadCountries();
</script>
@endsection
