@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Lead: {{ $lead->company_name }}</h4>
        <div>
            <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <a href="{{ route('leads.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
        </div>
    </div>

    {{-- Flash Messages --}}
    
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


    <div class="row gx-3">
        {{-- Left Column --}}
        <div class="col-md-6">
            <!-- Lead Details -->
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title mb-3">Contact Details</h4>
                    <p><strong>ID:</strong> {{ $lead->lead_code }}</p>
                    <p><strong>Contact Person:</strong> {{ $lead->contact_person }}</p>
                    <p><strong>Designation:</strong> {{ $lead->designation ?? '—' }}</p>
                    <p><strong>Phone:</strong> {{ $lead->phone ?? '—' }}</p>
                    <p><strong>Email:</strong> {{ $lead->email ?? '—' }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge 
                            @if($lead->status == 'New') bg-primary 
                            @elseif($lead->status == 'In Progress') bg-warning 
                            @elseif($lead->status == 'Converted') bg-success 
                            @else bg-danger @endif">
                            {{ $lead->status }}
                        </span>
                    </p>
                    <p><strong>Lead Source:</strong> {{ $lead->source ?? '—' }}</p>
                    <p><strong>Sub Source:</strong> {{ $lead->source_sub ?? '—' }}</p>

                    {{-- Convert to Customer --}}
                    @if(strtolower($lead->status) !== 'converted')
                        <form id="convertForm" action="{{ route('leads.convert', $lead->id) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="button" id="convertBtn" class="btn btn-success">
                                Convert to Customer
                            </button>
                        </form>
                    @else
                        <span class="badge bg-success mt-3">Already Converted</span>
                    @endif
                </div>
            </div>

            <!-- ============================
     PROJECT CREATION MODAL
============================= -->
<div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Projects Before Conversion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="projectForm">
                @csrf
                <div class="modal-body">

                    <div id="projectFieldsContainer">
                        <!-- JS will inject each project block here -->
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        Save Projects & Continue
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


            
<!-- Services & Costs -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-3">Services & Costs</h5>

        @php
            $groupedCosts = $lead->serviceCosts->groupBy(fn($c) => $c->service?->name ?? 'Unknown Service');
        @endphp

        @if($groupedCosts->isEmpty())
            <p class="text-muted">No service costs available.</p>
        @else
            @foreach($groupedCosts as $serviceName => $costs)
                <div class="mb-3 border rounded p-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-primary">{{ $serviceName }}</h6>
                        <span class="badge bg-light text-dark">
                            Total: ₹{{ number_format($costs->sum('amount'), 2) }}
                        </span>
                    </div>

                    <table class="table table-sm table-bordered mb-2">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Billing Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($costs as $cost)
                                <tr>
                                    <td>{{ $cost->name }}</td>
                                    <td>₹{{ number_format($cost->amount, 2) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $cost->billing_type)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <div class="text-end border-top pt-2 fw-semibold">
                Total Estimated Cost:
                ₹{{ number_format($lead->serviceCosts->sum('amount'), 2) }}
            </div>
        @endif
    </div>
</div>


        </div>

        {{-- Right Column --}}
        <div class="col-md-6">
            <!-- Address Details -->
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title mb-3">Address Details</h4>
                    <p><strong>Address Line 1:</strong> {{ $lead->address_line1 ?? '—' }}</p>
                    <p><strong>Address Line 2:</strong> {{ $lead->address_line2 ?? '—' }}</p>
                    <p><strong>Country:</strong> {{ $lead->country ?? '—' }}</p>
                    <p><strong>State:</strong> {{ $lead->state ?? '—' }}</p>
                    <p><strong>District:</strong> {{ $lead->district ?? '—' }}</p>
                    <p><strong>City:</strong> {{ $lead->city ?? '—' }}</p>
                    <p><strong>Pincode:</strong> {{ $lead->pincode ?? '—' }}</p>
                </div>
            </div>

            <!-- Add Follow-up Form -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Add Follow-up</h5>
                    <form action="{{ route('followups.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                        <div class="mb-2">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" required>{{ old('notes') }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Next Follow-up (optional)</label>
                            <input type="datetime-local" name="next_followup_date" class="form-control" value="{{ old('next_followup_date') }}">
                        </div>
                        <button class="btn btn-success btn-sm" type="submit">Save Follow-up</button>
                    </form>
                </div>
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
@endsection

@push('scripts')
<script>

// Pass services from Blade
const servicesSelected = <?php echo json_encode($lead->services); ?>;

document.addEventListener('DOMContentLoaded', function () {

    let container = document.getElementById("projectFieldsContainer");
    container.innerHTML = "";

    let companyName = "<?php echo $lead->company_name; ?>";

    servicesSelected.forEach(service => {

        let autoProjectName = companyName + " - " + service.name;

        container.innerHTML += `
            <div class="border rounded p-3 mb-3">

                <h6 class="text-primary mb-3">Service: <strong>${service.name}</strong></h6>

                <div class="mb-3">
                    <label class="form-label">Project Name <span class="text-danger">*</span></label>
                    <input type="text"
                        name="project_names[${service.id}]"
                        class="form-control"
                        value="${autoProjectName}"
                        required>
                </div>
                
                 <!-- ⭐ NEW SHORT DESCRIPTION FIELD ADDED HERE -->
        <div class="mb-3">
            <label class="form-label">Short Description</label>
            <input type="text"
                   name="short_descriptions[${service.id}]"
                   class="form-control"
                   placeholder="Enter short description">
        </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="descriptions[${service.id}]"
                              class="form-control"
                              rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label>Assign To <span class="text-danger">*</span></label>
                    <select name="assigned_to[${service.id}]" class="form-control" required>
                        <option value="">-- Select Staff --</option>
                        <?php foreach($staff as $user): ?>
                            <option value="<?= $user->id ?>"><?= $user->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date[${service.id}]" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>End Date</label>
                        <input type="date" name="end_date[${service.id}]" class="form-control">
                    </div>
                </div>

            </div>
        `;

    });

});


document.getElementById('convertBtn').addEventListener('click', function () {

    let customerName = "{{ $lead->company_name }}";

    if (servicesSelected.length > 0) {
        let firstService = servicesSelected[0];
        let firstInput = document.querySelector(`input[name="project_names[${firstService.id}]"]`);
        if (firstInput) {
            firstInput.value = `${customerName} - ${firstService.name}`;
        }
    }

    new bootstrap.Modal(document.getElementById('projectModal')).show();
});



// PROJECT FORM SUBMIT
document.getElementById('projectForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let projectModal = bootstrap.Modal.getInstance(document.getElementById('projectModal'));
    projectModal.hide();

    setTimeout(() => {

        Swal.fire({
            title: 'Convert Lead to Customer?',
            text: "Multiple projects will be created before conversion.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Convert & Create Project',
            cancelButtonText: 'Cancel'
        }).then((result) => {

            if (!result.isConfirmed) return;

            let formData = new FormData(document.getElementById('projectForm'));

            fetch("{{ route('projects.fromLead', $lead->id) }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
                .then(res => res.json())
                .then(response => {

                    if (response.status === "success") {
                        document.getElementById('convertForm').submit();
                    } else {
                        Swal.fire('Error', response.message ?? 'Project creation failed!', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Server error occurred!', 'error');
                });

        });

    }, 300);

});
</script>


</script>

@endpush
