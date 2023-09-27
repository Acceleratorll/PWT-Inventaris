
<form action="{{ route('product.destroy',['product' => $id]) }}" method="post">
    <a href="{{ route('product.edit', ['product' => $id]) }}"class="edit btn btn-success edit">
        Edit
    </a>
    @csrf
    @method("DELETE")
    <button type="submit" id="delete-compnay" data-original-title="Delete" class="delete btn btn-danger">
        Delete
    </button>
</form>