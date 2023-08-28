@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
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
        <div class="col-md-3">
            <x-adminlte-callout theme="info" title="Information">
                Info theme callout!
            </x-adminlte-callout>
        </div>
        <div class="col-md-3">
            <x-adminlte-callout theme="success" title="Success">
                Success theme callout!
            </x-adminlte-callout>
        </div>
        <div class="col-md-3">
        <x-adminlte-callout theme="warning" title="Warning">
            Warning theme callout!
        </x-adminlte-callout>
        </div>
        <div class="col-md-3">
            <x-adminlte-callout theme="danger" title="Danger">
                Danger theme callout!
            </x-adminlte-callout>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="p-6 m-20 bg-white rounded shadow">
                <x-adminlte-card title="Info Barang" theme="lightblue" theme-mode="outline" icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info" removable>
                    <div class="justify-content-between">
                        {{ $categoryChart->container() }}
                    </div>
                    @foreach($categories as $category)
                    @if ($category->id == 3)
                    <hr class="divider">
                    <div class="d-flex justify-content-between">
                        <span class="text-danger font-weight-bold">{{ $category->name }}</span>
                        <span class="text-danger font-weight-bold" style="margin-right: 15px;">{{ $category->products->count() }}</span>
                    </div>
                    @else
                    <hr class="divider">
                    <div class="d-flex justify-content-between">
                        <span class="text-black">{{ $category->name }}</span>
                        <span class="text-black" style="margin-right: 15px;">{{ $category->products->count() }}</span>
                    </div>
                    @endif
                    @endforeach
                    <hr class="divider">
                    <div class="d-flex justify-content-between">
                        <span class="text-black"><strong>Total Seluruh Barang</strong></span>
                        <span class="text-black" style="margin-right: 15px;"><strong>{{ $total }}</strong></span>
                    </div>
                </x-adminlte-card>
            </div>
        </div>
        <div class="col-md-8">
            <div class="rounded shadow">
                <x-adminlte-card title="Info Rencana Proses Produksi" theme="lightblue" theme-mode="outline" icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info" removable>
                    {!! $chart->container() !!}
                </x-adminlte-card>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <x-adminlte-card title="Unused Barang" icon="fas fa-trash" theme="danger" theme-mode="outline">
            <div class="d-flex justify-content-between">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead class="thead-light" id="table">
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Name</th>
                                <th scope="col" class="text-center">Last Used</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </x-adminlte-card>
    </div>
    <div class="col-md-8">
        <div class="rounded shadow">
            <x-adminlte-card title="Info Rencana Proses Produksi" theme="lightblue" theme-mode="outline" icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info" removable>
                {!! $monthlyUsedTintaChart->container() !!}
            </x-adminlte-card>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">

    </div>
    <div class="col-md-8">
        
    </div>
</div>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    @stop
    
    @section('js')

    <script src="{{ $monthlyUsedTintaChart->cdn() }}"></script>
    {{ $monthlyUsedTintaChart->script() }}
    <script src="{{ $categoryChart->cdn() }}"></script>
    {{ $categoryChart->script() }}
    <script src="{{ $chart->cdn() }}"></script>
    {{ $chart->script() }}

    <script>
        $(function() {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                lengthChange: false,
                paginate: true,
                pageLength: 8,
                ajax: '{{ route('get-unused-products') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'last_used', name: 'last_used' },
                ],
                rawColumns: ['last_used'],
            });
        });
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop
