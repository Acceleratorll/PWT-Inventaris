<form action="{{ route('profile.destroy', ['profile' => $id]) }}" method="post">
    <a href="{{ route('profile.edit', ['profile' => $id]) }}" class="edit btn btn-success">
        Edit
    </a>

    @csrf
    @method("DELETE")
    <button type="submit" id="delete-company" data-original-title="Delete" class="delete btn btn-danger">
        Delete
    </button>
</form>
