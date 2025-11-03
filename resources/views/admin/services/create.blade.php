@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Add New Service</h4>

    <form action="{{ route('services.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Service Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>

        <hr>
        <h5>Service Cost Details</h5>

        <div id="costContainer">
            <div class="row g-3 cost-row mb-2">
                <div class="col-md-3">
                    <input type="text" name="cost_name[]" class="form-control" placeholder="Cost Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="cost_amount[]" step="0.01" class="form-control" placeholder="Amount" required>
                </div>
                <div class="col-md-3">
                    <select name="billing_type[]" class="form-select" required>
                        <option value="">Select Billing Type</option>
                        <option value="one_time">One Time</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="per_sqft">Per Sq Ft</option>
                        <option value="per_head">Per Head</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <button type="button" class="btn btn-success addRow">+</button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">Save</button>
        <a href="{{ route('services.index') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>

<script>
document.addEventListener('click', function(e) {
    // ✅ Add new row
    if (e.target.classList.contains('addRow')) {
        const container = document.getElementById('costContainer');
        const firstRow = container.querySelector('.cost-row');
        const newRow = firstRow.cloneNode(true);

        // clear values
        newRow.querySelectorAll('input, select').forEach(el => el.value = '');

        // make the button a remove button
        const btn = newRow.querySelector('button');
        btn.classList.remove('btn-success', 'addRow');
        btn.classList.add('btn-danger', 'removeRow');
        btn.textContent = '–';

        container.appendChild(newRow);
    }

    // ✅ Remove row
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('.cost-row').remove();
    }
});
</script>
@endsection
