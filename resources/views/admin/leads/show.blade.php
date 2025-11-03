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
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
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

            

            <!-- Services -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Services</h5>
                    @if($lead->services->isEmpty())
                        <p class="text-muted">No services selected.</p>
                    @else
                        @foreach($lead->services as $service)
                            <span class="badge bg-info me-1">{{ $service->name }}</span>
                        @endforeach
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

            <!-- Follow-up History -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Follow-up History</h5>
                    @if($lead->followups->isEmpty())
                        <p class="text-muted">No follow-ups yet.</p>
                    @else
                        <ul class="list-group">
                            @foreach($lead->followups->sortByDesc('created_at') as $f)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $f->staff?->name ?? 'System' }}</strong>
                                            <small class="text-muted"> — {{ $f->created_at->format('d M Y, H:i') }}</small>
                                            <p class="mb-1">{{ $f->notes }}</p>
                                            @if($f->next_followup_date)
                                                <small class="text-info">Next: {{ \Carbon\Carbon::parse($f->next_followup_date)->format('d M Y, H:i') }}</small>
                                            @endif
                                        </div>
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
document.getElementById('convertBtn').addEventListener('click', function (e) {
    e.preventDefault();

    Swal.fire({
        title: 'Convert Lead to Customer?',
        text: "Are you sure you want to proceed?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Convert',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('convertForm').submit();
        }
    });
});
</script>
@endpush
