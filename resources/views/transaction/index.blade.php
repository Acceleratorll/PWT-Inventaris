@extends('adminlte::page')

@section('title', 'Product Transaction')

@section('content_header')
    <h1>Product Transaction</h1>
@stop

@section('content')
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
    </div>
</div>
<div class="row" style="height: 10px"></div>
<div class="row" >
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="button-action" style="margin-bottom: 20px">
                    <a class="btn btn-primary" href="{{ route('transaction.create') }}">
                        <span>+ Add</span>
                    </a>
                    <a class="btn btn-success" data-toggle="modal" data-target="#importModal">
                        <span>Import</span>
                    </a>
                    <a class="btn btn-secondary" href="{{ route('export.transactions') }}">
                        <span>Excel</span>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <caption>List of Product Transactions</caption>
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Code</th>
                                <th scope="col" class="text-center">Supplier</th>
                                <th scope="col" class="text-center">Tanggal Beli</th>
                                <th scope="col" class="text-center">Barang</th>
                                <th scope="col" class="text-center">Last Updated</th>
                                <th scope="col" class="text-center">Dibuat</th>
                                <th scope="col" class="text-center" width="14%">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Code</th>
                                <th scope="col" class="text-center">Supplier</th>
                                <th scope="col" class="text-center">Tanggal Beli</th>
                                <th scope="col" class="text-center">Barang</th>
                                <th scope="col" class="text-center">Last Updated</th>
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
                    <form action="{{ route('import.transactions') }}" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <label for="file">Choose File</label>
                                <input type="file" class="form-control-file" id="file" name="file">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary">Import</button>
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
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
        $(function() {

            $('#table tfoot th').each( function (i) {
                var title = $('#table thead th').eq( $(this).index() ).text();
                $(this).html( '<input type="text" placeholder="'+title+'" data-index="'+i+'" />' );
            });

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                searchable: true,
                scrollCollapse: true,
                scrollX: true,
                scrollY: 350,
                columnDefs: [{ width: 550, targets: 4 }],
                ajax: '{{ route('get-transactions') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'code', name: 'code' },
                    { data: 'supplier', name: 'supplier' },
                    { data: 'formatted_purchase_date', name: 'formatted_purchase_date' },
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
                var incomingProducts = data.incoming_products;
                
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