@extends('adminlte::page')

@section('title', 'Barang Keluar')

@section('content_header')
    <h1>Barang Keluar Status Selesai</h1>
@stop

@section('content')
<div class="row" style="height: 10px"></div>
<div class="row" >
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="button-action" style="margin-bottom: 20px">
                    @can('create rpp')
                    <a class="btn btn-primary" href="{{ route('rpp.create') }}">
                        <span>+ Tambah</span>
                    </a>
                    <a class="btn btn-success" data-toggle="modal" data-target="#importModal">
                        <span>Import</span>
                    </a>
                    @endcan
                    <a class="btn btn-secondary" href="{{ route('export.processplans') }}">
                        <span>Export</span>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <caption>Daftar Barang Keluar</caption>
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Kode</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Pelanggan</th>
                                <th scope="col" class="text-center">Tanggal Beli</th>
                                <th scope="col" class="text-center">Barang</th>
                                <th scope="col" class="text-center">Diubah</th>
                                <th scope="col" class="text-center">Dibuat</th>
                                <th scope="col" class="text-center" width="14%">Tindakan</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Kode</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Pelanggan</th>
                                <th scope="col" class="text-center">Tanggal Keluar</th>
                                <th scope="col" class="text-center">Barang</th>
                                <th scope="col" class="text-center">Diubah</th>
                                <th scope="col" class="text-center">Dibuat</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal fade" id="incomingProductsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Incoming Products</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <ul id="incoming-products-list"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('import.processplans') }}" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <label for="file">Choose File</label>
                                <input type="file" class="form-control-file" id="file" name="file">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary">Import</button>
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
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

    $(function() {
        $('#table tfoot th').each( function (i) {
            var title = $('#table thead th').eq( $(this).index() ).text();
            $(this).html( '<input type="text" placeholder="'+title+'" data-index="'+i+'" />' );
        });

        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            searchable: true,
            scrollCollapse: true,
            scrollX: true,
            scrollY: 350,
            columnDefs: [{ width: 550, targets: 4 }],
            ajax: '{{ route('get-rpps-finish') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'code', name: 'code' },
                { data: 'status', name: 'status' },
                { data: 'customer', name: 'customer' },
                { data: 'outed_date', name: 'outed_date' },
                { data: 'products', name: 'products' },
                { data: 'formatted_updated_at', name: 'formatted_updated_at' },
                { data: 'formatted_created_at', name: 'formatted_created_at' },
                { data: 'action', name: 'action', orderable: false },
            ],
            order:[[0, 'desc']],
        });

        $(table.table().container() ).on( 'keyup', 'tfoot input', function () {
            table
                .column( $(this).data('index') )
                .search( this.value )
                .order([])
                .draw();
        });

        $('#table tbody').on('click', '#show-incoming-products', function () {
            var data = $('#table').DataTable().row($(this).parents('tr')).data();
            var incomingProducts = data.product_processplans;
            
            var modal = $('#incomingProductsModal');
            var modalList = modal.find('#incoming-products-list');
            modalList.empty();
            
            console.log(incomingProducts);
            $.each(incomingProducts, function(index, product) {
                let qualifier = product.product.qualifier;
            
                modalList.append('<li>' + product.product.name + ' : ' + product.qty +
                    (' ' + qualifier.name ?? 'N/A') +'</li>');
            });
            
            modal.modal('show');
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop