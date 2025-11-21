@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="text-xl font-semibold mb-4">Employees</h2>

        {{-- Show Add Employee button only to Super Admin or HR --}}
        @role('Super Admin|HR')
        <div class="d-flex align-items-center mb-3">
            <a href="{{ route('users.create') }}" class="btn btn-primary me-3">
                <i class="ri-user-add-line"></i> Add Employee
            </a>

            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" id="manageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Manage Roles & Permissions
                </button>
                <ul class="dropdown-menu" aria-labelledby="manageDropdown">
                    <li><a class="dropdown-item" href="{{ route('roles.index') }}">Manage Roles</a></li>
                    <li><a class="dropdown-item" href="{{ route('permissions.index') }}">Manage Permissions</a></li>
                </ul>
            </div>
        </div>
        @endrole

        <div class="table-responsive">
            <table id="users-table" class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("users.index") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'employee_id', name: 'employeeDetail.employee_id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'department', name: 'employeeDetail.department.name' },
            { data: 'position', name: 'employeeDetail.position.title' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });
});
</script>
@endpush
