@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>RPP</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<div class="container">
<div class="row">
    <div class="col-md-12">
        @if($message = Session::get('success'))
        <div class="alert alert-success" role="alert">
            {{ $message }}
        </div>
        @elseif($message =  Session::get('error'))
        <div class="alert alert-danger" role="alert">
            {{ $message }}
        </div>
        @endif
        <div class="row" style="height: 10px"></div>
        <div class="card">
            <div class="card-body">
                <div class="button-action" style="margin-bottom: 20px">
                    <button type="button" class="btn btn-primary" onclick="location.href='/rpp/create'">
                        <span>+ Add</span>
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <caption>List of Employees</caption>
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Code</th>
                                <th scope="col" class="text-center">Customer</th>
                                <th scope="col" class="text-center">Order Type</th>
                                <th scope="col" class="text-center" width="14%">Action</th>
                            </tr>
                        </thead>\
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

    @stop
    
    @section('css')
    {{-- <link rel="stylesheet" href="{{ asset('css/table.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    @stop
    
    @section('js')
    <script>
        $(function() {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('get-rpps') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'code', name: 'code' },
                    { data: 'customer', name: 'customer' },
                    { data: 'order_type', name: 'order_type' },
                    { data: 'action', name: 'action', orderable: false },
                ]
            });
            console.log(data);
        });
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop