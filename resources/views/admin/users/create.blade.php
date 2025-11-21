@extends('layouts.app')

@section('title', 'Add New Employee')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">Add New Employee</h4>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                {{-- Name --}}
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" autocomplete="off" required>
                </div>

                {{-- Password --}}
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" autocomplete="new-password" class="form-control" required>
                </div>

                {{-- Confirm Password --}}
                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" class="form-control" required>
                </div>

                {{-- Department --}}
                <div class="col-md-6">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Position --}}
                <div class="col-md-6">
                    <label class="form-label">Position <span class="text-danger">*</span></label>
                    <select name="position_id" class="form-select" required>
                        <option value="">Select Position</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->title ?? $pos->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Role --}}
                <div class="col-md-6">
                    <label class="form-label">Assign Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Salary --}}
                <div class="col-md-6">
                    <label class="form-label">Salary</label>
                    <input type="number" name="salary" value="{{ old('salary') }}" class="form-control">
                </div>

                {{-- Joining Date --}}
                <div class="col-md-4">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="form-control">
                </div>

                {{-- Next Increment Date --}}
                <div class="col-md-4">
                    <label class="form-label">Next Increment Date</label>
                    <input type="date" name="next_increment_date" value="{{ old('next_increment_date') }}" class="form-control">
                </div>

                {{-- Relieving Date --}}
                <div class="col-md-4">
                    <label class="form-label">Relieving Date</label>
                    <input type="date" name="relieving_date" value="{{ old('relieving_date') }}" class="form-control">
                </div>

                {{-- Photo Upload --}}
                <div class="col-md-6">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" accept="image/*" onchange="previewPhoto(event)" class="form-control">
                    <img id="photoPreview" class="mt-2 rounded border" width="120" height="120" style="display:none;">
                </div>

                {{-- KYC Document Upload --}}
                <div class="col-md-6">
                    <label class="form-label">KYC Document</label>
                    <input type="file" name="kyc_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-3-line me-1"></i> Save Employee
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-light border px-4 ms-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Image Preview Script --}}
@push('scripts')
<script>
    function previewPhoto(event) {
        const preview = document.getElementById('photoPreview');
        preview.src = URL.createObjectURL(event.target.files[0]);
        preview.style.display = 'block';
    }
</script>
@endpush
@endsection
