@extends('adminlte::page')

@section('title', 'Kategori')

@section('content_header')
    <h1>Kategori Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <form action="{{ route('category.store') }}" method="post">
                @csrf
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input fgroup-class="mb-3" name="name" label="Name" placeholder="Masukkan Nama Kategori" disable-feedback/>
                </div>
                <div class="col-md-6">
                    <x-adminlte-input type="number" fgroup-class="mb-3" name="min" label="Minimal" placeholder="Masukkan Minimal Tahun Barang Ganti Kategori" disable-feedback/>
                </div>
            </div>
            <input value="Submit" class="form-control btn btn-outline-success" type="submit"/>
            </form>
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
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop