@extends('adminlte::page')

@section('title', 'Kategori')

@section('content_header')
    <h1>Kategori Barang</h1>
@stop

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="categoryName">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="name">
                    </div>
                    <div class="form-group">
                        <label for="min">Minimal</label>
                        <input type="number" class="form-control" id="min" name="min">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <div class="button-action" style="margin-bottom: 20px">
                <a href="{{ route('category.create') }}" class="btn btn-primary">+ Add</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="table">
                    <caption>Table Kategori Barang</caption>
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center">ID</th>
                            <th scope="col" class="text-center">Name</th>
                            <th scope="col" class="text-center">Minimal</th>
                            <th scope="col" class="text-center">Last Update</th>
                            <th scope="col" class="text-center">Created At</th>
                            <th scope="col" class="text-center" width="14%">Action</th>
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
            
        $(function() {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('get-categories') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'min', name: 'min' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false },
                ]
            });
        });
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop