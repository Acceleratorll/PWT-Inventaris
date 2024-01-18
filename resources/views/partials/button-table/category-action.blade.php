@can('update category')
<a href="{{route('category.edit', ['category' => $id])}}" class="edit btn btn-success">
    Ubah
</a>
@endcan
@can('delete category')
<button id="delete" data-id="{{ $id }}" data-name="{{ $name }}" data-original-title="Delete" class="delete btn btn-danger">
Hapus
</button>
@endcan
<form action="{{ route('category.destroy',['category' => $id]) }}" id="deleteForm" method="post">
    @csrf
    @method("DELETE")
</form>

<script>
    $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
    
            Swal.fire({
                title: 'Delete category',
                text: 'Are you sure you want to delete this category?',
                type: 'warning',
icon: 'warning',
icon: 'warning',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                console.log(result);
                if (result.value == true) {
                    console.log('confirmed');
                    $.ajax({
                        type: 'POST',
                        url: `{{ route("category.destroy", ["category" => ":categoryId"]) }}`.replace(':categoryId', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'category Deleted Successfully',
                                type: 'success',
icon: 'success',
type: 'success',
                                timer: 1700,
                            });
                            Swal.showLoading();
    
                            $('#table').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to delete category', 'error');
                        },
                    });
                }
            });
        });
    });
</script>