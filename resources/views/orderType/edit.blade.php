@extends('adminlte::page')

@section('title', 'Lokasi')

@section('content_header')
    <h1>Edit Location Barang</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('location.update', ['location' => $data->id]) }}" id="input-form" method="post">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-3">
                    <x-adminlte-input fgroup-class="mb-3" value="{{ $data->name }}" name="name" label="Name" placeholder="Masukkan Nama Location" disable-feedback/>
                </div>
                <div class="col-md-3">
                    <x-adminlte-input fgroup-class="mb-3" value="{{ $data->location }}" name="location" label="Exact Location" placeholder="Masukkan Location" disable-feedback/>
                </div>
                <div class="col-md-6">
                    <label for="desc">Deskripsi</label>
                    <textarea name="desc" id="desc" class="form-control mb-3" placeholder="Description">{{ $data->desc }}</textarea>
                </div>
            </div>
            <button class="form-control btn btn-outline-success" type="button" onclick="confirmation()">Simpan</button>
            </form>
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
            title: 'Error',
            text: '{{ Session::get('error') }}',
            
        });
    }

    if ('{{ Session::has('success') }}') {
        Swal.fire({
            icon: 'success',
            type: 'success',title: 'Success',
            timer: 3000,
            text: '{{ Session::get('success') }}',
            
        });
    }

    function confirmation() {
        Swal.fire({
            title: 'Apakah anda sudah yakin ?',
            text: 'Apakah anda sudah yakin dengan inputan anda ?',
            type: 'warning',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#input-form').submit();
            }
        });
    }
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop