@extends('layouts.app')

@section('title', 'Add New Role')

@section('content')
<div class="card mt-4">
    <div class="card-body">
        <h4 class="card-title mb-4">Add New Role</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            {{-- Role Name --}}
            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            {{-- Permissions --}}
            <div class="mb-4">
                <label class="form-label d-block">Assign Permissions</label>
                <div class="row g-2">
                    @foreach($permissions as $perm)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm-{{ $perm->id }}">
                                <label class="form-check-label" for="perm-{{ $perm->id }}">
                                    {{ ucfirst($perm->name) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Buttons --}}
            <div class="text-end">
                <a href="{{ route('roles.index') }}" class="btn btn-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
