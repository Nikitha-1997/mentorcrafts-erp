@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Add New Setting</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('settings.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Group</label>
                    <input type="text" name="group" class="form-control" placeholder="e.g. lead" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Key</label>
                    <input type="text" name="key" class="form-control" placeholder="e.g. sources" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categories & Sub Values</label>
                    <div id="categories">
                        <div class="category mb-3 border p-3 rounded">
                            <input type="text" name="categories[0][name]" class="form-control mb-2" placeholder="Category name (e.g. social_media)">
                            <div class="sub-values mb-2">
                                <input type="text" name="categories[0][sub_values][]" class="form-control mb-2" placeholder="Sub value (e.g. instagram)">
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-sub">+ Add Sub Value</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-category">+ Add Category</button>
                </div>

                <button class="btn btn-primary">Save Setting</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('add-category').addEventListener('click', function() {
    let index = document.querySelectorAll('.category').length;
    let categoryHtml = `
        <div class="category mb-3 border p-3 rounded">
            <input type="text" name="categories[${index}][name]" class="form-control mb-2" placeholder="Category name (e.g. social_media)">
            <div class="sub-values mb-2">
                <input type="text" name="categories[${index}][sub_values][]" class="form-control mb-2" placeholder="Sub value (e.g. instagram)">
            </div>
            <button type="button" class="btn btn-sm btn-success add-sub">+ Add Sub Value</button>
            <button type="button" class="btn btn-sm btn-danger remove-category mt-2">Remove Category</button>
        </div>
    `;
    document.getElementById('categories').insertAdjacentHTML('beforeend', categoryHtml);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-sub')) {
        let container = e.target.closest('.category').querySelector('.sub-values');
        let index = Array.from(document.querySelectorAll('.category')).indexOf(e.target.closest('.category'));
        let input = document.createElement('input');
        input.type = 'text';
        input.name = `categories[${index}][sub_values][]`;
        input.classList.add('form-control', 'mb-2');
        input.placeholder = "Sub value (e.g. facebook)";
        container.appendChild(input);
    }

    if (e.target.classList.contains('remove-category')) {
        e.target.closest('.category').remove();
    }
});
</script>
@endsection
