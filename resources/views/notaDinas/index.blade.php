@extends('adminlte::page')

@section('title', 'Nota Dinas')

@section('content_header')
    <h1>Nota Dinas</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="card-body">
            <div class="button-action" style="margin-bottom: 20px">
                @can('create nota dinas')
                <a href="{{ route('notaDinas.create') }}" class="btn btn-primary">+ Tambah</a>
                <a class="btn btn-success" data-toggle="modal" data-target="#importModal">
                    <span>Import</span>
                </a>
                @endcan
                <a class="btn btn-secondary" href="{{ route('export.nota-dinas') }}">
                    <span>Excel</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="table">
                    <caption>Tabel Nota Dinas</caption>
                    <thead class="thead-light">
                        <tr>
                            <th></th>
                            <th scope="col" class="text-center">ID</th>
                            <th scope="col" class="text-center">Kode</th>
                            <th scope="col" class="text-center">Periode</th>
                            <th scope="col" class="text-center">Otorisasi</th>
                            <th scope="col" class="text-center">Diubah</th>
                            <th scope="col" class="text-center">Dibuat</th>
                            <th scope="col" class="text-center">Deskripsi</th>
                            <th scope="col" class="text-center" width="14%">Tindakan</th>
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
            <form action="{{ route('import.nota-dinas') }}" method="POST" enctype="multipart/form-data">
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
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    if ('{{ Session::has('error') }}') {
        Swal.fire({
            icon: 'error',
            type: 'error',
            title: 'Error',
            // timer: 3000,
            text: '{{ Session::get('error') }}',
            onOpen: function() {
                Swal.showLoading()
            }
        });
    }

    if ('{{ Session::has('success') }}') {
        Swal.fire({
            icon: 'success',
            type: 'success',
            title: 'Success',
            // timer: 3000,
            text: '{{ Session::get('success') }}',
            onOpen: function() {
                Swal.showLoading()
            }
        });
    }

    var url = '{{ route('get-table-nota-dinas') }}';
    if ('{{ auth()->user()->getRoleNames()[0] }}' == 'logistik'){
        url = '{{ route('get-table-nota-dinas-authorized') }}';
        console.log('User', 'True Logistik');
        console.log('URL', url);
    }else{
        console.log('User', 'False not Logistik');
        console.log('URL', url);
    }

    const column = [
        { data: null, defaultContent: '', className: 'dt-control', orderable: false },
        { data: 'id', name: 'id' },
        { data: 'code', name: 'code' },
        { data: 'period', name: 'period' },
        { data: 'authorized', name: 'authorized' },
        { data: 'updated_at', name: 'updated_at' },
        { data: 'created_at', name: 'created_at' },
        { data: 'desc', name: 'desc' },
        { data: 'action', name: 'action', orderable: false },
    ];

    $(document).ready(function() {
        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            columns: column,
        });

        var detailRows = [];
    
        function format(d) {
            let productsArray = JSON.parse(d.products);
            let productList = `
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc;">
                            <th style="padding: 8px; text-align: left;">Nama</th>
                            <th style="padding: 8px; text-align: left;">Required</th>
                            <th style="padding: 8px; text-align: left;">Saldo</th>
                            <th style="padding: 8px; text-align: left;">Procurement Plan</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            productsArray.forEach(product => {
                productList += `
                    <tr style="border-bottom: 1px solid #ccc;">
                        <td style="padding: 8px; text-align: left;">${product.product.name}</td>
                        <td style="padding: 8px; text-align: left;">${product.requirement_amount}</td>
                        <td style="padding: 8px; text-align: left;">${product.product_amount}</td>
                        <td style="padding: 8px; text-align: left;">${product.procurement_plan_amount}</td>
                    </tr>`;
            });

            productList += `
                    </tbody>
                </table>
            `;

            return 'Products:<br>' + productList;
        }
    
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
    });


    function add() {
        Swal.fire({
            title: 'Add NotaDinas',
            input: 'text',
            inputLabel: 'Name',
            inputPlaceholder: 'Enter notadinas name',
            showCancelButton: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Name cannot be empty';
                }
            },
        }).then((result) => {
            if (result.isConfirmed) {
                var name = result.value;
                saveNotaDinas(name);
            }
        });
    }

    function saveNotaDinas(name) {
        $.ajax({
            type: 'POST',
            url: '{{ route("notaDinas.store") }}',
            data: {
                name: name,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                Swal.fire({
                    title: `NotaDinas Name "${name}" created successfully`, 
                    type: 'success',
                    icon: "success",
                    timer: 1700,
                });
                Swal.showLoading();

                var dataTable = $('#table').DataTable();
                dataTable.ajax.reload();
            },
            error: function (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to create notadinas', 'error');
            },
        });
    }
</script>
@stop
