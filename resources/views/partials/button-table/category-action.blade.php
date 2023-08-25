
<form action="{{ route('category.destroy',['category' => $id]) }}" method="post">
<a href="javascript:void(0)" data-toggle="tooltip" onClick="editFunc({{ $id }})" data-original-title="Edit" class="edit btn btn-success edit">
Edit
</a>
    @csrf
    @method("DELETE")
    <button type="submit" id="delete-compnay" data-original-title="Delete" class="delete btn btn-danger">
        Delete
    </button>
</form>