@if ($id == auth()->user()->id || auth()->user()->can('update user'))
<a href="{{ route('profile.edit', ['profile' => $id]) }}" class="edit btn btn-success">
    Edit
</a>
@endif
@can('delete user')
<button id="delete" data-id="{{ $id }}" data-name="{{ $name }}" data-original-title="Delete" class="delete btn btn-danger">
    Delete
</button>
@endcan
<form action="{{ route('profile.destroy',['profile' => $id]) }}" id="deleteForm" method="post">
    @csrf
    @method("DELETE")
</form>

<script>
    $(document).ready(function() {

    $('.delete').on('click', function () {
        var deleteButton = $(this);
        var defaultId = deleteButton.data('id');

        Swal.fire({
            title: 'Delete Profile',
            text: 'Are you sure you want to delete this profile?',
            type: 'warning',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
                $.ajax({
                    type: 'POST',
                    url: `{{ route("profile.destroy", ["profile" => ":profileId"]) }}`.replace(':profileId', defaultId),
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Profile Deleted Successfully',
                            type: 'success',
                            icon: 'success',
                            timer: 1700,
                        });
                        Swal.showLoading();

                        $('#myTable').DataTable().ajax.reload();
                    },
                    error: function (error) {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to delete profile', 'error');
                    },
                });
        });
    });
    });
</script>