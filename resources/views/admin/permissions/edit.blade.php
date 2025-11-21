@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content')
<div class="card mt-4">
    <div class="card-body">
        <h4 class="card-title mb-4">Edit Permission</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Permission Name --}}
            <div class="mb-3">
                <label class="form-label">Permission Name</label>
                <input type="text" name="name" value="{{ old('name', $permission->name) }}" class="form-control" required>
            </div>

            {{-- Buttons --}}
            <div class="text-end">
                <a href="{{ route('permissions.index') }}" class="btn btn-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Permission</button>
            </div>
        </form>
    </div>
</div>
@endsection
