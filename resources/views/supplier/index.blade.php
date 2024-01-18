@extends('adminlte::page')

@section('title', 'Supplier')

@section('content_header')
    <h1>Supplier</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <div class="button-action" style="margin-bottom: 20px">
                @can('create supplier')
                <button class="btn btn-primary" onclick="add()">+ Tambah</button>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="table">
                    <caption>Tabel Supplier</caption>
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center">ID</th>
                            <th scope="col" class="text-center">Nama</th>
                            <th scope="col" class="text-center">Diubah</th>
                            <th scope="col" class="text-center">Dibuat</th>
                            <th scope="col" class="text-center" width="14%">Tindakan</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<form action="{{ route('supplier.store') }}" method="post" id="createForm">
    @csrf
    <input type="text" name="name" id="name" hidden>
</form>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    if ('{{ Session::has('error') }}') {
        Swal.fire({
            icon: 'error',
            type: 'error',
            timer: 3000,
            title: 'Error',
            text: '{{ Session::get('error') }}',
        });
    }

    if ('{{ Session::has('success') }}') {
        Swal.fire({
            icon: 'success',
            type: 'success',
            title: 'Success',
            timer: 3000,
            text: '{{ Session::get('success') }}',
        });
    }
        
    const column = [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'formatted_updated_at', name: 'formatted_updated_at' },
        { data: 'formatted_created_at', name: 'formatted_created_at' },
        { data: 'action', name: 'action', orderable: false },
    ];

    $(document).ready(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('get-suppliers') }}',
            columns: column,
        });
    });

    function add() {
        Swal.fire({
            title: 'Add Supplier',
            input: 'text',
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
                saveSupplier(name);
            }
        });
    }

    function saveSupplier(name) {
        $.ajax({
            type: 'POST',
            url: '{{ route("supplier.store") }}',
            data: {
                name: name,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                Swal.fire({
                    title: `Supplier Name "${name}" created successfully`, 
                    type: 'success',
                    icon: "success",
                    timer: 1700,
                });
                Swal.showLoading();

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
@stop
