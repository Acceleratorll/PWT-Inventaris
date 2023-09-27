@extends('adminlte::page')

@section('title', 'Profile Page')

@section('content-header')
    <h1>Profile</h1>
@endsection

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
                <a class="btn btn-primary" href="{{route('profile.create')}}">
                    <span>+ Add</span>
                </a>
                <a class="btn btn-success" data-toggle="modal" data-target="#importModal">
                    <span>Import</span>
                </a>
                <a class="btn btn-secondary" href="{{ route('export.profiles') }}">
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
                            <th scope="col" class="text-center">Email</th>
                            <th scope="col" class="text-center">Role</th>
                            <th scope="col" class="text-center">Last Updated</th>
                            <th scope="col" class="text-center">Dibuat</th>
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
            <form action="{{ route('import.profiles') }}" method="POST" enctype="multipart/form-data">
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
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('css/rowColor.css') }}">
@stop
    
@section('js')
    <script>
        var columns = [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { 
                data: 'email', 
                name: 'email',
            },
            { data: 'role_name', name: 'role_name' },
            { data: 'formatted_updated_at', name: 'formatted_updated_at' },
            { data: 'formatted_created_at', name: 'formatted_created_at' },
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
                ajax: '{{ route('get-profiles') }}',
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
            .column( '3:visible' )
            .order( 'desc' )
            .draw();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop