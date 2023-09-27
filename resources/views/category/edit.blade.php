@extends('adminlte::page')

@section('title', 'Kategori')

@section('content_header')
    <h1>Edit Kategori Barang</h1>
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
            <form action="{{ route('category.edit', ['category' => $category->id]) }}" method="post">
                @csrf
                @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input fgroup-class="mb-3" name="name" label="Name" value="{{ $category->name }}" placeholder="Masukkan Nama Kategori" disable-feedback/>
                </div>
                <div class="col-md-6">
                    <x-adminlte-input fgroup-class="mb-3" name="max" label="Max Tahun" value="{{ $category->max }}" placeholder="Masukkan Maximal Tahun Barang Ganti Kategori" disable-feedback/>
                </div>
            </div>
            <button class="form-control btn btn-success" type="submit">Save</button>
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
        
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop