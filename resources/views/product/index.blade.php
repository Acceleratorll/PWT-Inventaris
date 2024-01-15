@extends('adminlte::page')

@section('title', 'Product')

@section('content_header')
    <h1>List Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <div class="button-action" style="margin-bottom: 20px">
            @can('create product')
                <a class="btn btn-primary" href="{{route('product.create')}}">
                    <span>+ Add</span>
                </a>
                <a class="btn btn-success" data-toggle="modal" data-target="#importModal">
                    <span>Import</span>
                </a>
            @endcan
                <a class="btn btn-secondary" href="{{ route('export.products') }}">
                    <span>Excel</span>
                </a>
            </div>
            <div class="button-action" style="margin-bottom: 20px">
                <label for="stock-filter">Stock Filter:</label>
                <select id="stock-filter">
                    <option value="all">All</option>
                    <option value="Daily">Daily</option>
                    <option value="Slow Moving">Slow Moving</option>
                    <option value="Unused">Unused</option>
                </select>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="table">
                    <caption>Table Barang</caption>
                    <thead class="thead-light">
                        <tr>
                            <th></th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
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

    var selectElement = document.getElementById('stock-filter');
    var maxAmountArray = [];
    var detailRows = [];
    var columns = [
        { data: null, defaultContent: '', className: 'dt-control', orderable: false },
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
        { data: 'total_amount', name: 'total_amount', orderable: true},
        { data: 'minimal_amount', name: 'minimal_amount' },
        { data: 'updated_at', name: 'updated_at' },
        { data: 'created_at', name: 'created_at' },
        { data: 'note', name: 'note' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ];

    // $('#table thead tr')
    // .clone(true)
    // .addClass('filters')
    // .appendTo('#table thead');

    function format(d) {
        let productTransArray = JSON.parse(d.product_transactions);
        let outProArray = JSON.parse(d.outgoing_products);

        // Combine product_transactions and outgoing_products and sort by date
        let combinedHistory = [...outProArray, ...productTransArray];
        combinedHistory.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
        console.log(combinedHistory);

        let historyList = `
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #ccc;">
                        <th style="padding: 8px; text-align: left;">Date</th>
                        <th style="padding: 8px; text-align: left;">Code</th>
                        <th style="padding: 8px; text-align: left;">Details</th>
                        <th style="padding: 8px; text-align: left;">Type</th>
                        <th style="padding: 8px; text-align: left;">Amount</th>
                        <th style="padding: 8px; text-align: left;">Original</th>
                    </tr>
                </thead>
                <tbody>
        `;

        

        combinedHistory.forEach(entry => {
            let updatedDate = new Date(entry.updated_at);
            let formattedDate = updatedDate.toLocaleDateString('us-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            historyList += `
                <tr style="border-bottom: 1px solid #ccc;">
                    <td style="padding: 8px; text-align: left;">${formattedDate}</td>
                    <td style="padding: 8px; text-align: left;">${entry.transaction_id ? entry.transaction.code : entry.process_plan.code}</td>
                    <td style="padding: 8px; text-align: left;">${entry.transaction_id ? entry.transaction.supplier.name : entry.process_plan.customer.name}</td>
                    <td style="padding: 8px; text-align: left;">${entry.transaction_id ? 'Income' : 'Out'}</td>
                    <td style="padding: 8px; text-align: left;">${entry.amount} ${d.qualifier.name}</td>
                    <td style="padding: 8px; text-align: left;">${entry.product_amount} ${d.qualifier.name}</td>
                </tr>`;
        });

        historyList += `
                </tbody>
            </table>
        `;

        return 'History:<br>' + historyList;
    }
    
    $(function() {
        var table = $('#table').DataTable({
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

                if (amount < maxAmount) {
                    amountCell.css({'color': 'red', 'font-weight': 'bold'});
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

        $('#table tbody').on('click', 'td.dt-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
         
            if (row.child.isShown()) {
                row.child.hide();
            }
            else {
                row.child(format(row.data())).show();
            }
        });

        table.on('draw', function () {
            detailRows.forEach(function (id, i) {
                $('#' + id + ' td.dt-control').trigger('click');
            });
        });

        $(table.table().container() ).on( 'keyup', 'tfoot input', function () {
                table
                    .column( $(this).data('index') )
                    .search( this.value )
                    .order([])
                    .draw();
        });

        selectElement.addEventListener('change', function() {
            var selectedValue = selectElement.value;

            if (selectedValue === 'warning') {
                console.log(selectedValue);
                table.ajax.url('{{ route('get-warning-products') }}').load();
            } else if (selectedValue === 'error') {
                console.log(selectedValue);
                table.ajax.url('{{ route('get-danger-products') }}').load();
            } else if(selectedValue === 'Daily' || selectedValue === 'Slow Moving' || selectedValue === 'Unused'){
                console.log(selectedValue);
                var url = "{{ route('get-products-by-category', ['category' => ":category"]) }}";
                url = url.replace(':category', selectedValue);
                table.ajax.url(url).load();
            } else {
                console.log(selectedValue);
                table.ajax.url('{{ route('get-all-products') }}').load();
            }
        });

        // table
        // .column( '7:visible' )
        // .order( 'asc' )
        // .draw();
    });
</script>
@stop