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
                <button type="button" class="btn btn-primary" href="{{route('product.create')}}">
                    <span>+ Add</span>
                </button>
                <a class="btn btn-success" data-toggle="modal" data-target="#importModal">
                    <span>Import</span>
                </a>
                <a class="btn btn-secondary" href="{{ route('export.products') }}">
                    <span>Excel</span>
                </a>
            </div>
            <div class="button-action" style="margin-bottom: 20px">
                <label for="stock-filter">Stock Filter:</label>
                <select id="stock-filter">
                    <option value="all">All</option>
                    <option value="warning">Warning (30%)</option>
                    <option value="error">Error (10%)</option>
                </select>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="myTable">
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
                            <th scope="col" class="text-center">Last Updated</th>
                            <th scope="col" class="text-center">Dibuat</th>
                            <th scope="col" class="text-center">Keterangan</th>
                            <th scope="col" class="text-center" width="14%">Action</th>
                        </tr>
                    </thead>
                </table>
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
            <form action="{{ route('import.products') }}" method="POST" enctype="multipart/form-data">
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

@stop
    
    @section('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
        <link rel="stylesheet" href="{{ asset('css/rowColor.css') }}">

        <style>
            .red-color {
                border: 2px solid red;
                background-color: red !important;
            }
            
            .orange-color {
                border: 2px solid orange;
                background-color: orange !important;
            }
            </style>
    @stop
    
    @section('js')
    <script>
        var selectElement = document.getElementById('stock-filter');
        var maxAmountArray = [];
        var columns = [
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
            { data: 'amount', name: 'amount', orderable: true},
            { data: 'max_amount', name: 'max_amount' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'created_at', name: 'created_at' },
            { data: 'note', name: 'note' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ];

        $('#myTable thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#myTable thead');

        $(function() {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                searchable: true,
                ajax: '{{ route('get-all-products') }}',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                columns: columns,
                dom: 'Bfrtip',
                colReorder: true,
                select: true,
                rowCallback: function (row, data, dataIndex) {
                    var amountCell = $(row).find('td:eq(7)');
                    var maxAmountCell = $(row).find('td:eq(8)');
                    var amount = parseFloat(amountCell.text());
                    var maxAmount = parseFloat(maxAmountCell.text());
                    maxAmountArray.push(maxAmount);

                    if (amount < (0.10 * maxAmount)) {
                        amountCell.css({'color': 'red', 'font-weight': 'bold'});
                    } else if (amount < (0.30 * maxAmount)) {
                        amountCell.css({'color': 'orange', 'font-weight': 'bold'});
                    }
                },
                initComplete: function () {
                    var api = this.api();
                    
                    api
                    .columns()
                    .eq(0)
                    .each(function (colIdx) {
                        var cell = $('.filters th').eq(
                            $(api.column(colIdx).header()).index()
                        );
                        var title = $(cell).text();
                        $(cell).html('<input type="text" placeholder="' + title + '" />');
                        
                        $(
                            'input',
                            $('.filters th').eq($(api.column(colIdx).header()).index())
                        )
                        
                        .off('keyup change')
                        .on('change', function (e) {
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})';
                            
                            var cursorPosition = this.selectionStart;
                            api
                            .column(colIdx)
                            .search(
                                this.value != ''
                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                : '',
                                this.value != '',
                                this.value == ''
                            )
                            .draw();
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();
                            
                            $(this).trigger('change');
                            $(this)
                            .focus()[0]
                            .setSelectionRange(cursorPosition, cursorPosition);
                        });
                    });
                },
            });

            selectElement.addEventListener('change', function() {
                var selectedValue = selectElement.value;

                if (selectedValue === 'warning') {
                    console.log(selectedValue);
                    table.ajax.url('{{ route('get-warning-products') }}').load();
                } else if (selectedValue === 'error') {
                    console.log(selectedValue);
                    table.ajax.url('{{ route('get-danger-products') }}').load();
                } else {
                    console.log(selectedValue);
                    table.ajax.url('{{ route('get-all-products') }}').load();
                }
            });

            table
            .column( '7:visible' )
            .order( 'asc' )
            .draw();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop