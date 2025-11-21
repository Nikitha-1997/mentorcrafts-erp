@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">All Follow-ups</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Lead</th>
                        <th>Contact Person</th>
                        <th>Notes</th>
                        <th>Next Follow-up</th>
                        <th>Added By</th>
                        <th>Added On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($followups as $f)
                        <tr>
                            <td><a href="{{ route('leads.show', $f->lead->id) }}">{{ $f->lead->company_name }}</a></td>
                            <td>{{ $f->lead->contact_person }}</td>
                            <td>{{ Str::limit($f->notes, 50) }}</td>
                            <td>
                                @if($f->next_followup_date)
                                    {{ \Carbon\Carbon::parse($f->next_followup_date)->format('d M Y, H:i') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $f->staff?->name ?? 'System' }}</td>
                            <td>{{ $f->created_at->format('d M Y') }}</td>
                            <td>
                                <form action="{{ route('followups.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Delete this follow-up?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $followups->links() }}
        </div>
    </div>
</div>
@endsection
