@can('view product location')
<a href="{{ route('productLocation.edit', ['productLocation' => $id]) }}" data-original-title="Edit" class="edit btn btn-success edit">
    Edit
</a>
@endcan
<button id="show-incoming-products" data-id="{{ $id }}" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
    Details
</button>
@can('delete product location')
<button id="delete" data-id="{{ $id }}" data-original-title="Delete" class="delete btn btn-danger">
Delete
</button>
@endcan

<script>
    $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
    
            Swal.fire({
                title: 'Delete Product Location',
                text: 'Are you sure you want to delete this product location?',
                type: 'warning',
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
                        url: `{{ route("productLocation.destroy", ["productLocation" => ":Id"]) }}`.replace(':Id', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Product Location Deleted Successfully',
                                type: 'success',
                                icon: 'success',
                                timer: 1700,
                            });
                            Swal.showLoading();
    
                            $('#myTable').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to delete product product location', 'error');
                        },
                    });
                }
            });
        });
    });
</script>