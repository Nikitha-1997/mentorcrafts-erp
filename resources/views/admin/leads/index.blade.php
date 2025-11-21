@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Leads</h4>
        <a href="{{ route('leads.create') }}" class="btn btn-primary">Add Lead</a>
    </div>

    {{-- 🔍 Search & Filter Form --}}
    
<form id="filterForm" class="row g-3 align-items-end mb-3">
    <div class="col-md-3 filter-field">
        <label for="search" class="form-label mb-1">Search</label>
        <input type="text" id="search" name="search" class="form-control" placeholder="Company, service, status, source...">
    </div>

    
    <div class="col-md-2 filter-field">
        <label for="source" class="form-label mb-1">Source</label>
        <select id="source" name="source" class="form-select">
            <option value="">All Sources</option>
            <option value="Website">Website</option>
            <option value="Referral">Referral</option>
            <option value="Social Media">Social Media</option>
            <option value="Advertisement">Advertisement</option>
        </select>
    </div>

    <div class="col-md-2 filter-field">
        <label for="month" class="form-label mb-1">Month</label>
        <input type="month" id="month" name="month" class="form-control">
    </div>

    <div class="col-md-3 d-flex gap-2 align-items-end">
        <button type="button" id="filterBtn" class="btn btn-primary flex-fill">Filter</button>
        <button type="button" id="clearBtn" class="btn btn-secondary flex-fill">Clear</button>
    </div>
</form>


    {{-- ✅ Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 📋 Leads DataTable --}}
    <div class="card">
        <div class="card-body">
            <table id="leadsTable" class="table table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Company</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Services</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Last Followup</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
/* Ensure uniform height and spacing */
.filter-field {
    display: flex;
    flex-direction: column;
    justify-content: end;
}
.form-label {
    font-weight: 500;
}
</style>
@endpush


@push('scripts')
<script>
$(function () {
    let table = $('#leadsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/leads/data",
            data: function (d) {
                d.search = $('#search').val();
                d.status = $('#status').val();
                d.source = $('#source').val();
                d.month = $('#month').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: true },
            { data: 'company_name', name: 'company_name' },
            { data: 'contact_person', name: 'contact_person' },
            { data: 'phone', name: 'phone' },
            { data: 'services', name: 'services', orderable: false, searchable: true },
            { data: 'status', name: 'status' },
            { data: 'source', name: 'source' },
            { data: 'last_followup', name: 'last_followup' },
            { data: 'actions', name: 'actions', orderable: false, searchable: true }
        ]
    });


    $('#filterBtn').on('click', function() {
        table.ajax.reload();
    });

    $('#clearBtn').on('click', function() {
        $('#filterForm')[0].reset();
        table.ajax.reload();
    });
});
</script>
@endpush
