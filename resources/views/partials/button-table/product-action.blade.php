@can('update product')
<a href="{{ route('product.edit', ['product' => $id]) }}"class="btn btn-success">
    Edit
</a>
@endcan
@can('view transaction')
<button class="masuk btn btn-info text-white" id="masuk" data-original-title="Masuk" data-id="{{ $id }}" data-name="{{ $name }}">Income</button>
@endcan
@can('view rpp')
<button class="keluar btn btn-info text-white" id="keluar" data-original-title="Keluar" data-id="{{ $id }}" data-name="{{ $name }}">Out</button>
@endcan
@can('delete product')
<button id="delete" data-id="{{ $id }}" data-name="{{ $name }}" data-original-title="Delete" class="delete btn btn-danger">
Delete
</button>
@endcan
<form action="{{ route('product.destroy',['product' => $id]) }}" id="deleteForm" method="post">
    @csrf
    @method("DELETE")
</form>

<script>
    $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
    
            Swal.fire({
                title: 'Delete Product',
                text: 'Are you sure you want to delete this product?',
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
                        url: `{{ route("product.destroy", ["product" => ":productId"]) }}`.replace(':productId', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'product Deleted Successfully',
                                type: 'success',
                                icon: 'success',
                                timer: 1700,
                                onOpen: function() {
                                    Swal.showLoading()
                                    $('#myTable').DataTable().ajax.reload();
                                }
                            });
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to delete product', 'error');
                        },
                    });
                }
            });
        });
    });
    </script>