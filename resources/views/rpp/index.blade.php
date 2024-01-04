@extends('adminlte::page')

@section('title', 'RPP')

@section('content_header')
    <h1>RPP</h1>
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
        <div class="row" style="height: 10px"></div>
        <div class="card">
            <div class="card-body">
                <div class="button-action" style="margin-bottom: 20px">
                    <a class="btn btn-primary" href="{{ route('rpp.create') }}">
                        <span>+ Add</span>
                    </a>
                    <a class="btn btn-success" data-toggle="modal" data-target="#importModal">
                        <span>Import</span>
                    </a>
                    <a class="btn btn-secondary" href="{{ route('export.processplans') }}">
                        <span>Excel</span>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <caption>List of Rpps</caption>
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Code</th>
                                <th scope="col" class="text-center">Customer</th>
                                <th scope="col" class="text-center">Order Type</th>
                                <th scope="col" class="text-center">Products</th>
                                <th scope="col" class="text-center">Last Updated</th>
                                <th scope="col" class="text-center">Dibuat</th>
                                <th scope="col" class="text-center">Description</th>
                                <th scope="col" class="text-center" width="14%">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Code</th>
                                <th scope="col" class="text-center">Customer</th>
                                <th scope="col" class="text-center">Order Type</th>
                                <th scope="col" class="text-center">Products</th>
                                <th scope="col" class="text-center">Last Updated</th>
                                <th scope="col" class="text-center">Dibuat</th>
                                <th scope="col" class="text-center">Description</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal fade" id="outgoingProductsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Outgoing Products</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <ul id="outgoing-products-list"></ul>
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
                            <button type="submit" class="btn btn-primary">Import</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    @stop
    
    @section('css')
    @stop
    
    @section('js')
    <script>
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
                ajax: '{{ route('get-rpps') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'code', name: 'code' },
                    { data: 'customer', name: 'customer' },
                    { data: 'order_type', name: 'order_type' },
                    { data: 'products', name: 'products' },
                    { data: 'formatted_updated_at', name: 'formatted_updated_at' },
                    { data: 'formatted_created_at', name: 'formatted_created_at' },
                    { data: 'desc', name: 'desc' },
                    { data: 'action', name: 'action', orderable: false },
                ],
                order: [[0, 'desc']],
            });

            $(table.table().container() ).on( 'keyup', 'tfoot input', function () {
                table
                    .column( $(this).data('index') )
                    .search( this.value )
                    .order([])
                    .draw();
            });

            $('#table tbody').on('click', '#show-outgoing-products', function () {
                var data = $('#table').DataTable().row($(this).parents('tr')).data();
                var outgoingProducts = data.outgoing_products;
                
                var modal = $('#outgoingProductsModal');
                var modalList = modal.find('#outgoing-products-list');
                modalList.empty();
                
                console.log(outgoingProducts);
                $.each(outgoingProducts, function(index, product) {
                
                    modalList.append('<li>' + product.product.name + ' : ' + product.qty +
                        (product.product.qualifier ? ' ' + product.product.qualifier.name : '') +'</li>');
                });
                
                modal.modal('show');
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop