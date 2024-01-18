@can('update transaction')
<a href="{{ route('transaction.edit', ['transaction' => $id]) }}" data-original-title="Edit" class="edit btn btn-success edit">
    Ubah
</a>
@endcan
@if($status == 0)
@can('create product location')
<a href="/transaction/placing/{{ $id }}" id="isi" data-id="{{ $id }}" class="isi btn btn-success">
    Process
</a>
@endcan
@endif
{{-- <button id="show-incoming-products" data-id="{{ $id }}" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
    Details
</button> --}}
@can('delete transaction')
<button id="delete" data-id="{{ $id }}" data-supplier="{{ $supplier_id }}" data-original-title="Delete" class="delete btn btn-danger">
    Hapus
</button>
@endcan

<script>
        $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
    
            Swal.fire({
                title: 'Delete Product Transaction',
                text: 'Are you sure you want to delete this product transaction?',
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
                        url: `{{ route("transaction.destroy", ["transaction" => ":Id"]) }}`.replace(':Id', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Product Transaction Deleted Successfully',
                                type: 'success',
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