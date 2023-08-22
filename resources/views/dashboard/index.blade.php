@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-callout theme="info" title="Information">
                Info theme callout!
            </x-adminlte-callout>
        </div>
        <div class="col-md-4">
            <x-adminlte-small-box title="528" text="Pegawai" icon="fas fa-user-plus text-teal"
            theme="primary" url="#" url-text="View all users"/>
        </div>
        <div class="col-md-4">
            <x-adminlte-callout theme="info" title="Information">
                Info theme callout!
            </x-adminlte-callout>
        </div>
    </div>
    
    
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-card title="Info Tinta" theme="info" icon="fas fa-lg fa-bell" collapsible removable maximizable>
                <x-adminlte-card title="Tinta Stock" theme="lightblue" theme-mode="outline"
                icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info"
                removable>
                <div class="d-flex justify-content-between">
                    <span class="text-danger font-weight-bold">Stock Tipis</span>
                    <span class="text-danger font-weight-bold" style="margin-right: 15px;">3</span>
                </div>
                <hr class="divider">
                <div class="d-flex justify-content-between">
                    <span class="text-black">Stock Normal</span>
                    <span class="text-black" style="margin-right: 15px;">100</span>
                </div>
                <hr class="divider">
                <div class="d-flex justify-content-between">
                    <span class="text-black">Stock Banyak</span>
                    <span class="text-black" style="margin-right: 15px;">10</span>
                </div>
            </x-adminlte-card>
            <div class="container px-4 mx-auto">
                {!! $chart->container() !!}
            </div><br>
            <label for="table4">Tinta Stock Tipis</label>
            <x-adminlte-datatable id="table4" :heads="$heads" theme="danger" :config="$config"
            striped hoverable>
            @foreach($config['data'] as $row)
            <tr>
                    @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                    @endforeach
                </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-card>
    </div>
    <div class="col-md-4">
        <x-adminlte-card title="Info Seluruh Bahan" theme="info" icon="fas fa-lg fa-bell" collapsible removable maximizable>
            <x-adminlte-card title="Stok Barang" theme="lightblue" theme-mode="outline"
            icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info"
            removable>
            <div class="d-flex justify-content-between">
                <span class="text-black">Daily</span>
                <span class="text-black" style="margin-right: 15px;">240</span>
            </div>
            <hr class="divider">
            <div class="d-flex justify-content-between">
                <span class="text-black">Slow</span>
                <span class="text-black" style="margin-right: 15px;">30</span>
            </div>
            <hr class="divider">
            <div class="d-flex justify-content-between">
                <span class="text-danger font-weight-bold">Unused Stock</span>
                <span class="text-danger font-weight-bold" style="margin-right: 15px;">10</span>
            </div>
        </x-adminlte-card>

        </x-adminlte-card>
    </div>
    <div class="col-md-4">
        <x-adminlte-card title="Info Kategori Barang" theme="info" icon="fas fa-lg fa-bell" collapsible removable maximizable>
            <x-adminlte-card title="Kategori Stok" theme="lightblue" theme-mode="outline"
            icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info"
            removable>
            <div class="d-flex justify-content-between">
                <span class="text-black">Daily</span>
                <span class="text-black" style="margin-right: 15px;">240</span>
            </div>
            <hr class="divider">
            <div class="d-flex justify-content-between">
                <span class="text-black">Slow</span>
                <span class="text-black" style="margin-right: 15px;">30</span>
            </div>
            <hr class="divider">
            <div class="d-flex justify-content-between">
                <span class="text-danger font-weight-bold">Unused Stock</span>
                <span class="text-danger font-weight-bold" style="margin-right: 15px;">10</span>
            </div>
        </x-adminlte-card>
            <div class="container px-4 mx-auto">
                {{ $kategoriChart->container() }}
            </div><br>
            <label for="daily">Barang Kategori Daily</label>
            <x-adminlte-datatable id="daily" :heads="$kheads" theme="danger" :config="$config"
            striped hoverable>
            @foreach($kconfig['data'] as $row)
            <tr>
                @foreach($row as $cell)
                <td>{!! $cell !!}</td>
                @endforeach
            </tr>
            @endforeach
        </x-adminlte-datatable>
    </x-adminlte-card>
        </div>
    </div>
    
    <div class="row">
    </div>

<script src="{{ $chart->cdn() }}"></script>
{{ $chart->script() }}
<script src="{{ $kategoriChart->cdn() }}"></script>
{{ $kategoriChart->script() }}

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="/css/divider.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
