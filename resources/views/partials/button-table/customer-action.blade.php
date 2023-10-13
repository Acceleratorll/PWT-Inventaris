<a href="#" class="edit btn btn-success"
id="edit"
   data-id="{{ $id }}"
   data-name="{{ $name }}">
    Edit
</a>
<button id="delete" data-id="{{ $id }}" data-name="{{ $name }}" data-original-title="Delete" class="delete btn btn-danger">
    Delete
</button>
<form action="{{ route('customer.destroy',['customer' => $id]) }}" id="deleteForm" method="post">
    @csrf
    @method("DELETE")
</form>
<form action="{{ route('customer.update', ['customer' => $id]) }}" id="editForm" method="post">
    @csrf
    @method('PUT')
    <input type="text" name="name" id="name" hidden>
</form>

<script>
    $(document).ready(function() {
    $('.edit').on('click', function () {
        var editButton = $(this);
        var defaultId = editButton.data('id');
        var defaultName = editButton.data('name');

        Swal.fire({
            title: 'Edit customer',
            input: 'text',
            inputValue: defaultName,
            inputLabel: 'Name',
            inputPlaceholder: 'Enter customer name',
            showCancelButton: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Name cannot be empty';
                }
            },
        }).then((result) => {
            if (result.isConfirmed) {
                var name = result.value;
                update(name, defaultId);
            }
        });
    });

    $('.delete').on('click', function () {
        var deleteButton = $(this);
        var defaultId = deleteButton.data('id');

        Swal.fire({
            title: 'Delete customer',
            text: 'Are you sure you want to delete this customer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: `{{ route("customer.destroy", ["customer" => ":customerId"]) }}`.replace(':customerId', defaultId),
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'customer Deleted Successfully',
                            icon: 'success',
                            timer: 1700,
                        });
                        Swal.showLoading();

                        $('#table').DataTable().ajax.reload();
                    },
                    error: function (error) {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to delete customer', 'error');
                    },
                });
            }
        });
    });
});

function update(name, defaultId)
{
    $.ajax({
    type: 'POST',
    url: `{{ route("customer.update", ["customer" => ":customerId"]) }}`.replace(':customerId', defaultId),
    data: {
        name: name,
        _token: '{{ csrf_token() }}',
        _method: 'PUT'
    },
    success: function (response) {
        Swal.fire({
            title: `customer Name "${name}" Updated Successfully`, 
            icon: 'success',
            timer: 1700,
        });
        Swal.showLoading();
        
        var dataTable = $('#table').DataTable();
        dataTable.ajax.reload();
    },
    error: function (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to create customer', 'error');
    },
});
}
    
</script>