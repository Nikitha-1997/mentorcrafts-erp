@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Services</h4>
        <a href="{{ route('services.create') }}" class="btn btn-primary">+ Add Service</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="services-table" class="table table-bordered table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 180px;">Service Name</th>
                            <th style="width: 300px;">Description</th>
                            <th style="width: 130px;">Total Cost</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 120px;">By</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Make buttons stay inline */
    .table .text-nowrap {
        white-space: nowrap;
    }

    /* Prevent wrapping and align numbers */
    .text-end {
        text-align: right;
    }

    /* Better spacing for compact layout */
    table.dataTable td, table.dataTable th {
        vertical-align: middle;
    }

    /* Responsive behavior */
    @media (max-width: 991px) {
        table.dataTable th, table.dataTable td {
            white-space: normal !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {
    $('#services-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        ajax: <?php echo json_encode(route('services.data')); ?>,

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-nowrap' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description', className: 'text-wrap' },
            { data: 'total_cost', name: 'total_cost', orderable: false, searchable: false, className: 'text-end text-nowrap' },
            { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center text-nowrap' },
            { data: 'created_by', name: 'created_by', className: 'text-nowrap' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-nowrap text-center' },
        ],
        order: [[1, 'asc']],
        columnDefs: [
            { targets: [0, 3, 4, 5, 6], width: 'auto' },
        ],
        drawCallback: function() {
            // enable bootstrap tooltips if used in actions
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                    new bootstrap.Tooltip(el);
                });
            }
        }
    });
});
</script>
@endpush
