<div class="d-flex justify-content-center gap-1">
    @if(!empty($viewRoute))
        <a href="{{ $viewRoute }}" class="btn btn-sm btn-info">View</a>
    @endif

    @if(!empty($editRoute))
        <a href="{{ $editRoute }}" class="btn btn-sm btn-warning">Edit</a>
    @endif

    @if(!empty($deleteRoute))
        <form action="{{ $deleteRoute }}" method="POST" onsubmit="return confirm('Are you sure?')" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    @endif
</div>
