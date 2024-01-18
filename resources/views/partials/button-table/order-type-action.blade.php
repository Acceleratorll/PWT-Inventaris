@can('update type')
<button onclick="editOrderType({{ $id }}, '{{ $name }}')" data-original-title="Edit" class="edit btn btn-success edit">
    Edit
</button>
@endcan
<button id="show-outgoing-products" data-id="{{ $id }}" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
    Details
</button>
@can('delete type')
<button id="delete" data-id="{{ $id }}" data-original-title="Delete" class="delete btn btn-danger">
    Hapus
</button>
@endcan
<form action="{{ route('orderType.destroy',['orderType' => $id]) }}" id="deleteForm" method="post">
@csrf
@method("DELETE")
</form>

<script>
    $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
            Swal.fire({
                title: 'Delete ORDER TYPE',
                text: 'Are you sure you want to delete this ORDER TYPE?',
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
                        url: `{{ route("orderType.destroy", ["orderType" => ":ordertypeId"]) }}`.replace(':ordertypeId', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'ORDERTYPE Deleted Successfully',
                                type: 'success',
                                icon: 'success',
                                timer: 1700,
                                onOpen: function() {
                                    Swal.showLoading()
                                }
                            });
    
                            $('#table').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to delete ORDERTYPE', 'error');
                        },
                    });
                }
            });
        });
    });

    function editOrderType(id, currentName) {
        Swal.fire({
            title: 'Edit Order Type',
            input: 'text',
            inputValue: currentName,
            inputLabel: 'Name',
            inputPlaceholder: 'Enter updated order type name',
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Name cannot be empty';
                }
            },
        }).then((result) => {
            if (result.isConfirmed) {
                var updatedName = result.value;
                updateOrderType(id, updatedName);
            }
        });
    }
    
    function updateOrderType(id, updatedName) {
        $.ajax({
            type: 'POST',
            url: '{{ route("orderType.update", ["orderType" => ":orderType"]) }}'.replace(':orderType', id),
            data: {
                name: updatedName,
                _method: 'PUT',
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                 Swal.fire({
                    title: `Order Type ${updatedName} updated successfully`, 
                    type: 'success',
                    icon: "success",
                    timer: 1700,
                    onOpen: function() {
                        Swal.showLoading()
                    }
                });

                var dataTable = $('#table').DataTable();
                dataTable.ajax.reload();
            },
            error: function(error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to update order type', 'error');
            },
        });
    }
</script>