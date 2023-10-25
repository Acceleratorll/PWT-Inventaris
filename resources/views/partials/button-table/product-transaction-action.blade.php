    <a href="{{ route('productTransaction.edit', ['productTransaction' => $id]) }}" data-original-title="Edit" class="edit btn btn-success edit">
        Edit
    </a>
    <button id="show-incoming-products" data-id="{{ $id }}" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
        Details
    </button>
    <button id="delete" data-id="{{ $id }}" data-supplier="{{ $supplier_id }}" data-original-title="Delete" class="delete btn btn-danger">
Delete
</button>

<script>
        $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
    
            Swal.fire({
                title: 'Delete Transaction Product',
                text: 'Are you sure you want to delete this product transaction?',
                type: 'warning',
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
                        url: `{{ route("productTransaction.destroy", ["productTransaction" => ":Id"]) }}`.replace(':Id', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Product Transaction Deleted Successfully',
                                type: 'success',
icon: 'success',
                                icon: 'success',
                                timer: 1700,
                            });
                            Swal.showLoading();
    
                            $('#table').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to delete product transaction', 'error');
                        },
                    });
                }
            });
        });
    });
</script>