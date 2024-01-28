@extends('adminlte::page')

@section('title', 'Product Location')

@section('content_header')
    <h1>Daftar Product Location</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <div class="button-action" style="margin-bottom: 20px">
                {{-- <a class="btn btn-success" data-toggle="modal" data-target="#importModal">
                    <span>Import</span>
                </a> --}}
                <a class="btn btn-secondary" href="{{ route('export.productLocations') }}">
                    <span>Export</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="myTable">
                    <caption>Tabel Barang</caption>
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center">ID</th>
                            <th scope="col" class="text-center">Barang</th>
                            <th scope="col" class="text-center">Lokasi</th>
                            <th scope="col" class="text-center">Jumlah</th>
                            <th scope="col" class="text-center">Pembelian</th>
                            <th scope="col" class="text-center">Kadaluarsa</th>
                            <th scope="col" class="text-center">Diubah</th>
                            <th scope="col" class="text-center">Dibuat</th>
                            {{-- <th scope="col" class="text-center" width="14%">Tindakan</th> --}}
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
            <form action="{{ route('import.productLocations') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="file">Choose File</label>
                        <input type="file" class="form-control-file" id="file" name="file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Import</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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

    var selectElement = document.getElementById('stock-filter');
    var maxAmountArray = [];
    var columns = [
        { data: 'id', name: 'id' },
        { data: 'product', name: 'product' },
        { data: 'location', name: 'location' },
        { data: 'amount', name: 'amount' },
        { data: 'purchase_date', name: 'purchase_date' },
        { data: 'expired', name: 'expired' },
        { data: 'updated_at', name: 'updated_at' },
        { data: 'created_at', name: 'created_at' },
        // { data: 'action', name: 'action', orderable: false, searchable: false },
    ];

    $('#myTable thead tr')
    .clone(true)
    .addClass('filters')
    .appendTo('#myTable thead');

    function format(d) {
        let historyArray = JSON.parse(d.history);
        let historyList = `
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #ccc;">
                        <th style="padding: 8px; text-align: left;">Type</th>
                        <th style="padding: 8px; text-align: left;">Date</th>
                        <th style="padding: 8px; text-align: left;">Details</th>
                    </tr>
                </thead>
                <tbody>
        `;

        // Combine productLocation_transactions and outgoing_productLocations and sort by date
        let combinedHistory = [...historyArray.productLocation_transactions, ...historyArray.outgoing_productLocations];
        combinedHistory.sort((a, b) => new Date(b.date) - new Date(a.date));

        combinedHistory.forEach(entry => {
            historyList += `
                <tr style="border-bottom: 1px solid #ccc;">
                    <td style="padding: 8px; text-align: left;">${entry.type}</td>
                    <td style="padding: 8px; text-align: left;">${entry.date}</td>
                    <td style="padding: 8px; text-align: left;">${entry.details}</td>
                </tr>`;
        });

        historyList += `
                </tbody>
            </table>
        `;

        return 'History:<br>' + historyList;
    }
    
    $(function() {
        var table = $('#myTable').DataTable({
            processing: true,
            serverSide: true,
            searchable: true,
            ajax: '{{ route('table-productLocations') }}',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            columns: columns,
            dom: 'Bfrtip',
            colReorder: true,
            select: true,
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

        table
        .column( '7:visible' )
        .order( 'asc' )
        .draw();
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop