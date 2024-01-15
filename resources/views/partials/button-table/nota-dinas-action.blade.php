@can('update nota dinas')
<a href="{{ route('notaDinas.edit', ['notaDina' => $id]) }}" data-original-title="Edit" class="edit btn btn-success edit">
    Edit
</a>
@endcan
@can('delete nota dinas')
<button id="delete" data-id="{{ $id }}" data-original-title="Delete" class="delete btn btn-danger">
Delete
</button>
@endcan
<form action="{{ route('notaDinas.destroy',['notaDina' => $id]) }}" id="deleteForm" method="post">
    @csrf
    @method("DELETE")
</form>

<script>
    $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
            Swal.fire({
                title: 'Delete Nota Dinas',
                text: 'Are you sure you want to delete this Nota Dinas?',
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
                        url: `{{ route("notaDinas.destroy", ["notaDina" => ":notaDinasId"]) }}`.replace(':notaDinasId', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Nota Dinas Deleted Successfully',
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
                            Swal.fire('Error', 'Failed to delete Nota Dinas', 'error');
                        },
                    });
                }
            });
        });
    });
</script>