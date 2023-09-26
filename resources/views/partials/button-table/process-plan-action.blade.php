<form action="{{ route('rpp.destroy',['rpp' => $id]) }}" method="post">
    <a href="/rpp/{{ $id }}/edit" data-toggle="tooltip" onClick="editFunc({{ $id }})" data-original-title="Edit" class="edit btn btn-success edit">
        Edit
    </a>
    <button type="button" id="show-outgoing-products" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
        Details
    </button>
    @csrf
    @method("DELETE")
    <button
        type="button"
        id="delete-company"
        data-original-title="Delete"
        class="delete btn btn-danger"
        onclick="confirmDelete('{{ route('rpp.destroy', ['rpp' => $id]) }}')"
    >
    Delete
    </button>
</form>