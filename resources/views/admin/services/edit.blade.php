@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Edit Service</h4>

    <form action="{{ route('services.update', $service->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Service Name</label>
            <input type="text" name="name" value="{{ old('name', $service->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $service->description) }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" class="form-check-input" {{ $service->is_active ? 'checked' : '' }}>
            <label class="form-check-label">Active</label>
        </div>

        <hr>
        <h5>Service Cost Details</h5>

        <div id="costContainer">
            @foreach($service->costs as $cost)
            <div class="row g-3 cost-row mb-2">
                <div class="col-md-3">
                    <input type="text" name="cost_name[]" value="{{ $cost->name }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="cost_amount[]" value="{{ $cost->amount }}" step="0.01" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <select name="billing_type[]" class="form-select" required>
                        <option value="one_time" {{ $cost->billing_type == 'one_time' ? 'selected' : '' }}>One Time</option>
                        <option value="monthly" {{ $cost->billing_type == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $cost->billing_type == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        <option value="per_sqft" {{ $cost->billing_type == 'per_sqft' ? 'selected' : '' }}>Per Sq Ft</option>
                          <option value="per_head" {{ $cost->billing_type == 'per_head' ? 'selected' : '' }}>Per Head</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger removeRow">–</button>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-success addRow mt-2">+ Add Cost</button>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('addRow')) {
        const container = document.getElementById('costContainer');
        const template = document.querySelector('.cost-row');
        const newRow = template.cloneNode(true);
        newRow.querySelectorAll('input').forEach(i => i.value = '');
        container.appendChild(newRow);
        newRow.querySelector('.btn').classList.remove('btn-success', 'addRow');
        newRow.querySelector('.btn').classList.add('btn-danger', 'removeRow');
        newRow.querySelector('.btn').textContent = '–';
    }

    if (e.target.classList.contains('removeRow')) {
        e.target.closest('.cost-row').remove();
    }
});
</script>
@endsection
