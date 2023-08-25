
<form action="{{ route('rpp.destroy',['rpp' => $id]) }}" method="post">
    <a href="/rpp/{{ $id }}/edit" data-toggle="tooltip" onClick="editFunc({{ $id }})" data-original-title="Edit" class="edit btn btn-success edit">
        Edit
    </a>
    @csrf
    @method("DELETE")
    <button type="submit" id="delete-compnay" data-original-title="Delete" class="delete btn btn-danger">
        Delete
    </button>
</form>