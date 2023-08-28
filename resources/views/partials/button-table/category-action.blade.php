
<form action="{{ route('category.destroy',['category' => $id]) }}" method="post">
<a href="/category/{{ $id }}/edit" class="edit btn btn-success">
    Edit
</a>
    @csrf
    @method("DELETE")
    <button type="submit" id="delete-compnay" data-original-title="Delete" class="delete btn btn-danger">
        Delete
    </button>
</form>