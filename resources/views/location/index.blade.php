@extends('adminlte::page')

@section('title', 'Location')

@section('content_header')
    <h1>Location</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <div class="button-action" style="margin-bottom: 20px">
                <a href="{{ route('location.create') }}" class="btn btn-primary">+ Add</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="table">
                    <caption>Table Location</caption>
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center">ID</th>
                            <th scope="col" class="text-center">Name</th>
                            <th scope="col" class="text-center">Location</th>
                            <th scope="col" class="text-center">Last Update</th>
                            <th scope="col" class="text-center">Created At</th>
                            <th scope="col" class="text-center">Description</th>
                            <th scope="col" class="text-center" width="14%">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<form action="{{ route('location.store') }}" method="post" id="createForm">
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
    const column = [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'location', name: 'location' },
        { data: 'updated_at', name: 'updated_at' },
        { data: 'created_at', name: 'created_at' },
        { data: 'desc', name: 'desc' },
        { data: 'action', name: 'action', orderable: false },
    ];

    if ('{{ Session::has('error') }}') {
        Swal.fire({
            icon: 'error',
            type: 'error',
            title: 'Error',timer: 3000,
            text: '{{ Session::get('error') }}',
            onOpen: function() {
                Swal.showLoading()
            }
        });
    }

    if ('{{ Session::has('success') }}') {
        Swal.fire({
            icon: 'success',
            type: 'success',title: 'Success',
            timer: 3000,
            text: '{{ Session::get('success') }}',
            onOpen: function() {
                Swal.showLoading()
            }
        });
    }

    $(document).ready(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('get-locations') }}',
            columns: column,
        });
    });

    function add() {
        Swal.fire({
            title: 'Add Location',
            input: 'text',
            inputLabel: 'Name',
            inputPlaceholder: 'Enter location name',
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
                saveLocation(name);
            }
        });
    }

    function saveLocation(name) {
        $.ajax({
            type: 'POST',
            url: '{{ route("location.store") }}',
            data: {
                name: name,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                Swal.fire({
                    title: `Location Name "${name}" created successfully`, 
                    type: 'success',
icon: 'success',
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
                Swal.fire('Error', 'Failed to create location', 'error');
            },
        });
    }
</script>
@stop
