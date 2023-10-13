<form action="{{ route('rpp.destroy',['rpp' => $id]) }}" method="post" id="delete-form">
    <a href="{{ route('rpp.edit', ['rpp' => $id]) }}" data-original-title="Edit" class="edit btn btn-success edit">
        Edit
    </a>
    <button id="show-outgoing-products" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
        Details
    </button>
    @csrf
    @method("DELETE")
    <button
        type="submit"
        id="delete-company"
        {{-- data-original-title="Delete" --}}
        class="delete btn btn-danger"
        {{-- onclick="confirmDelete('{{ route('rpp.destroy', ['rpp' => $id]) }}')" --}}
    >
    Delete
    </button>
</form>

<script>
    document.getElementById('delete-company').addEventListener('click', function (event) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        });
    });
</script>