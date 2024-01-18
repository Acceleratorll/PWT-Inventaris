@extends('adminlte::page')

@section('title', 'Pelanggan')

@section('content_header')
    <h1>Pelanggan</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <div class="button-action" style="margin-bottom: 20px">
                @can('create customer')
                <button class="btn btn-primary" onclick="addModal()">+ Tambah</button>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="table">
                    <caption>Tabel Customer</caption>
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
@stop
    
    @section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    @stop
    
    @section('js')
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('get-customers') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'formatted_updated_at', name: 'formatted_updated_at' },
                    { data: 'formatted_created_at', name: 'formatted_created_at' },
                    { data: 'action', name: 'action', orderable: false },
                ],
            });
        });

        function addModal(){
            Swal.fire({
            title: 'Add customer',
            input: 'text',
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
                savecustomer(name);
            }
        });
        }

        function savecustomer(name) {
        $.ajax({
            type: 'POST',
            url: '{{ route("customer.store") }}',
            data: {
                name: name,
                _token: '{{ csrf_token() }}'
            },
            success: function () {
                Swal.fire({
                    title: `customer Name "${name}" created successfully`, 
                    type: 'success',
icon: 'success',
type: 'success',
                    icon: "success",
                    timer: 1700,
                });
                Swal.showLoading();
                console.log('After Loading...');
                $('#table').DataTable().ajax.reload();
            },
            error: function (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to create customer', 'error');
            },
        });
    }
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop