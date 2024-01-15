@can('update rpp')
<a href="{{ route('rpp.edit', ['rpp' => $id]) }}" data-original-title="Edit" class="edit btn btn-success edit">
    Edit
</a>
@endcan
<button id="show-outgoing-products" data-id="{{ $id }}" class="btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
    Details
</button>
@can('delete rpp')
<button id="delete" data-id="{{ $id }}" data-original-title="Delete" class="delete btn btn-danger">
Delete
</button>
@endcan
<form action="{{ route('rpp.destroy',['rpp' => $id]) }}" id="deleteForm" method="post">
    @csrf
    @method("DELETE")
</form>

<script>
    $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
            Swal.fire({
                title: 'Delete RPP',
                text: 'Are you sure you want to delete this RPP?',
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
                        url: `{{ route("rpp.destroy", ["rpp" => ":rppId"]) }}`.replace(':rppId', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'RPP Deleted Successfully',
                                type: 'success',
                                icon: 'success',
                                timer: 1700,
                            });
                            Swal.showLoading();
    
                            $('#table').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to delete RPP', 'error');
                        },
                    });
                }
            });
        });
    });
</script>