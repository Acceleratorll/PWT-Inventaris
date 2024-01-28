@can('update supplier')
<a href="#" class="edit btn btn-success"
id="edit"
   data-id="{{ $id }}"
   data-name="{{ $name }}">
    Ubah
</a>
@endcan
@can('delete supplier')
<button id="delete" data-id="{{ $id }}" data-name="{{ $name }}" data-original-title="Delete" class="delete btn btn-danger">
    Hapus
</button>
@endcan
<form action="{{ route('supplier.destroy',['supplier' => $id]) }}" id="deleteForm" method="post">
    @csrf
    @method("DELETE")
</form>
<form action="{{ route('supplier.update', ['supplier' => $id]) }}" id="editForm" method="post">
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
            title: 'Edit Supplier',
            input: 'text',
            inputValue: defaultName,
            inputLabel: 'Name',
            inputPlaceholder: 'Enter supplier name',
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
            title: 'Delete Supplier',
            text: 'Are you sure you want to delete this supplier?',
            type: 'warning',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: `{{ route("supplier.destroy", ["supplier" => ":supplierId"]) }}`.replace(':supplierId', defaultId),
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Supplier Deleted Successfully',
                            type: 'success',
icon: 'success',
type: 'success',
                            icon: 'success',
type: 'success',
                            timer: 1700,
                        });
                        

                        $('#table').DataTable().ajax.reload();
                    },
                    error: function (error) {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to delete supplier', 'error');
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
    url: `{{ route("supplier.update", ["supplier" => ":supplierId"]) }}`.replace(':supplierId', defaultId),
    data: {
        name: name,
        _token: '{{ csrf_token() }}',
        _method: 'PUT'
    },
    success: function (response) {
        Swal.fire({
            title: `Supplier Name "${name}" Updated Successfully`, 
            type: 'success',
icon: 'success',
type: 'success',
            icon: 'success',
type: 'success',
            timer: 1700,
        });
        
        
        var dataTable = $('#table').DataTable();
        dataTable.ajax.reload();
    },
    error: function (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to create supplier', 'error');
    },
});
}
    
</script>