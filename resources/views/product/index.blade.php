@extends('adminlte::page')

@section('title', 'Product')

@section('content_header')
    <h1>List Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        @if($message = Session::get('info'))
        <x-adminlte-alert theme="info" title="Info">
            {{ $message }}
        </x-adminlte-alert>
    </div>
    <div class="col-md-3">
        @elseif($message =  Session::get('success'))
        <x-adminlte-alert theme="success" title="Success">
            {{ $message }}
        </x-adminlte-alert>
    </div>
    <div class="col-md-3">
        @elseif($message =  Session::get('warning'))
        <x-adminlte-alert theme="warning" title="Warning">
            {{ $message }}
        </x-adminlte-alert>
    </div>
    <div class="col-md-3">
        @elseif($message =  Session::get('error'))
        <x-adminlte-alert theme="danger" title="Danger">
            {{ $message }}
        </x-adminlte-alert>
        @endif
    </div>
</div>
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <div class="button-action" style="margin-bottom: 20px">
                <button type="button" class="btn btn-primary" onclick="location.href='/product/create'">
                    <span>+ Add</span>
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="table">
                    <caption>Table Barang</caption>
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center">ID</th>
                            <th scope="col" class="text-center">Name</th>
                            <th scope="col" class="text-center">Material</th>
                            <th scope="col" class="text-center">Tipe Barang</th>
                            <th scope="col" class="text-center">Kategory</th>
                            <th scope="col" class="text-center">Code</th>
                            <th scope="col" class="text-center">Satuan</th>
                            <th scope="col" class="text-center">Stock</th>
                            <th scope="col" class="text-center">Stock Maksimal</th>
                            <th scope="col" class="text-center">Keterangan</th>
                            <th scope="col" class="text-center">Last Updated</th>
                            <th scope="col" class="text-center">Dibuat</th>
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
        $(function() {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                searchable: true,
                ajax: '{{ route('get-products') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { 
                        data: 'material_name', 
                        name: 'material_name',
                    },
                    { 
                        data: 'product_type_name', 
                        name: 'product_type_name',
                    },
                    { 
                        data: 'category_product_name', 
                        name: 'category_product_name',
                    },
                    { data: 'product_code', name: 'product_code' },
                    { 
                        data: 'qualifier_name', 
                        name: 'qualifier_name',
                    },
                    { data: 'amount', name: 'amount' },
                    { data: 'max_amount', name: 'max_amount' },
                    { data: 'note', name: 'note' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            console.log(data);
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop