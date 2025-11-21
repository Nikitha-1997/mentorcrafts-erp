@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="mb-4">{{ $customer->company_name }} - {{ $service->name }} Profile</h3>

    <form action="{{ route('customer.profile.update', [$customer->id, $service->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="service_name" value="{{ $service->name }}">

        @if(!empty($sections))
            <div class="accordion" id="profileAccordion">
                @foreach($sections as $sectionTitle => $subpoints)
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading-{{ Str::slug($sectionTitle) }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-{{ Str::slug($sectionTitle) }}"
                                aria-expanded="false"
                                aria-controls="collapse-{{ Str::slug($sectionTitle) }}">
                                {{ $sectionTitle }}
                            </button>
                        </h2>

                        <div id="collapse-{{ Str::slug($sectionTitle) }}" class="accordion-collapse collapse"
                            aria-labelledby="heading-{{ Str::slug($sectionTitle) }}"
                            data-bs-parent="#profileAccordion">

                            <div class="accordion-body">

                                @if(!empty($subpoints))
                                    @foreach($subpoints as $item)
                                        @php
                                            $statusKey = $sectionTitle . '|' . $item;
                                            $status = $statuses[$statusKey] ?? null;
                                        @endphp

                                        <div class="card mb-3 border">
                                            <div class="card-body">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                    <h6 class="fw-bold mb-0 me-3" style="min-width:180px;">{{ $item }}</h6>

                                                    <input type="hidden" name="subpoints[{{ $item }}][section]" value="{{ $sectionTitle }}">

                                                    {{-- Planning --}}
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="subpoints[{{ $item }}][planning]" value="1"
                                                            {{ $status && $status->planning ? 'checked' : '' }}>
                                                        <label class="form-check-label">Planning</label>
                                                    </div>

                                                    {{-- Documentation --}}
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="subpoints[{{ $item }}][documentation]" value="1"
                                                            {{ $status && $status->documentation ? 'checked' : '' }}>
                                                        <label class="form-check-label">Documentation</label>
                                                    </div>

                                                    {{-- Training --}}
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="subpoints[{{ $item }}][training]" value="1"
                                                            {{ $status && $status->training ? 'checked' : '' }}>
                                                        <label class="form-check-label">Training</label>
                                                    </div>

                                                    {{-- Implementation --}}
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="subpoints[{{ $item }}][implementation]" value="1"
                                                            {{ $status && $status->implementation ? 'checked' : '' }}>
                                                        <label class="form-check-label">Implementation</label>
                                                    </div>

                                                    {{-- File Upload --}}
                                                    <div class="ms-3">
                                                        {{-- ✅ Allow multiple file selection --}}
                                                        <input type="file" 
                                                            name="subpoints[{{ $item }}][files][]" 
                                                            class="form-control form-control-sm" 
                                                            style="width:200px;" 
                                                            multiple>

                                                        {{-- ✅ Display existing uploaded files if any --}}
                                                        @if($status && $status->file_path)
                                                            @php
                                                                $files = is_array($status->file_path)
                                                                    ? $status->file_path
                                                                    : json_decode($status->file_path, true);
                                                            @endphp

                                                            @if(!empty($files))
                                                                <div class="mt-2">
                                                                    <small>Uploaded Files:</small><br>
                                                                    @foreach($files as $index => $file)
                                                                        <a href="{{ asset('storage/'.$file) }}" 
                                                                        target="_blank"
                                                                        class="d-block mb-1">📎 File {{ $index + 1 }}</a>
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            {{-- Keep track of existing file paths (so they aren’t lost if no new upload) --}}
                                                            <input type="hidden" 
                                                                name="subpoints[{{ $item }}][existing_files]" 
                                                                value='{{ json_encode($files) }}'>
                                                        @endif
                                                    </div>

                                                    {{-- Remarks --}}
                                                    <div class="ms-3">
                                                        @php
    $remarksData = [];
    if ($status && $status->remarks) {
        $decoded = json_decode($status->remarks, true);
        $remarksData = is_array($decoded) ? $decoded : [];
    }
@endphp

<a href="javascript:void(0);"
   class="text-primary remark-link"
   data-bs-toggle="modal"
   data-bs-target="#remarksModal"
   data-item="{{ $item }}"
   data-section="{{ $sectionTitle }}"
   data-id="{{ $status->id ?? '' }}"
   data-remarks='@json($remarksData)'>
   Add/View Remarks
</a>



                                                    </div>
                                                </div>

                                                {{-- Hidden remarks field (will be updated via modal) --}}
                                               
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">No details available.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        @else
            <p class="text-muted">No profile sections found for this service.</p>
        @endif
    </form>
</div>

<!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="remarksModalLabel">Remarks</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
    <div id="remarksHistory" class="mb-3" style="max-height:200px; overflow-y:auto;">
        <p class="text-muted small">No previous remarks found.</p>
    </div>

    <label class="fw-bold small">Add New Remark:</label>
    <textarea id="remarksText" class="form-control" rows="3"></textarea>
</div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" id="saveRemarksBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentSubpointId = null;
    let currentRemarks = [];
    let isEditing = false;

    const remarksModalEl = document.getElementById('remarksModal');
    const remarksModal = bootstrap.Modal.getOrCreateInstance(remarksModalEl);

    // 🟢 Open Modal
    $('.remark-link').on('click', function () {
        currentSubpointId = $(this).data('id');
        let remarks = $(this).data('remarks');

        try {
            if (typeof remarks === 'string') remarks = JSON.parse(remarks);
        } catch {
            remarks = [];
        }
        if (!Array.isArray(remarks)) remarks = [];

        currentRemarks = remarks;
        const $history = $('#remarksHistory');
        $history.empty();

        if (remarks.length > 0) {
            remarks.forEach((r, i) => {
                $history.append(`
                    <div class="border rounded p-2 mb-2 bg-light">
                        <small><strong>${r.user}</strong> 
                        <span class="text-muted">(${r.time})</span></small>
                        <p class="mb-1">${r.text}</p>
                        <button class="btn btn-sm btn-link text-secondary edit-remark" data-index="${i}">✏️ Edit</button>
                    </div>
                `);
            });
        } else {
            $history.html('<p class="text-muted small">No previous remarks found.</p>');
        }

        isEditing = false; 
        $('#remarksText').val(''); // Clear input when modal opens
    });

    // 🟢 Save new remark
    $('#saveRemarksBtn').on('click', function () {
        const newRemark = $('#remarksText').val().trim();

        if (!isEditing && !newRemark) {
            alert('Please enter a remark.');
            return;
        }

        $.post({
            url: "{{ route('save.remark') }}",
            data: {
                _token: '{{ csrf_token() }}',
                subpoint_id: currentSubpointId,
                remark: newRemark
            },
            success: function (res) {
                $('#remarksText').val('');
                currentRemarks = res.remarks;

                const latest = res.remarks[res.remarks.length - 1];
                $('#remarksHistory').append(`
                    <div class="border rounded p-2 mb-2 bg-light">
                        <small><strong>${latest.user}</strong> 
                        <span class="text-muted">(${latest.time})</span></small>
                        <p class="mb-1">${latest.text}</p>
                        <button class="btn btn-sm btn-link text-secondary edit-remark" data-index="${res.remarks.length - 1}">✏️ Edit</button>
                    </div>
                `);

                isEditing = false;
                remarksModal.hide(); // ✅ Close modal automatically
            },
            error: () => alert('Failed to save remark.')
        });
    });

    // 🟢 Inline Edit
    $(document).on('click', '.edit-remark', function () {
        const index = $(this).data('index');
        const $text = $(this).siblings('p');
        const oldText = $text.text();

        const newText = prompt('Edit your remark:', oldText);
        if (!newText || newText === oldText) return;

        isEditing = true;

        $.post({
            url: "{{ route('update.remark') }}",
            data: {
                _token: '{{ csrf_token() }}',
                subpoint_id: currentSubpointId,
                index,
                remark: newText
            },
            success: function (res) {
                currentRemarks = res.remarks;
                $text.text(newText);
                isEditing = false;
                remarksModal.hide(); // ✅ Close modal automatically
            },
            error: () => {
                alert('Failed to update remark.');
                isEditing = false;
            }
        });
    });
});

</script>



@endsection
