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
                <a href="{{ route('notaDinas.create') }}" class="btn btn-primary">+ Add</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="table">
                    <caption>Table Nota Dinas</caption>
                    <thead class="thead-light">
                        <tr>
                            <th></th>
                            <th scope="col" class="text-center">ID</th>
                            <th scope="col" class="text-center">Code</th>
                            <th scope="col" class="text-center">Period</th>
                            <th scope="col" class="text-center">Authorized</th>
                            <th scope="col" class="text-center">Description</th>
                            <th scope="col" class="text-center">Last Update</th>
                            <th scope="col" class="text-center">Created At</th>
                            <th scope="col" class="text-center" width="14%">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
    const column = [
        { data: null, defaultContent: '', className: 'dt-control', orderable: false },
        { data: 'id', name: 'id' },
        { data: 'code', name: 'code' },
        { data: 'period', name: 'period' },
        { data: 'authorized', name: 'authorized' },
        { data: 'desc', name: 'desc' },
        { data: 'updated_at', name: 'updated_at' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false },
    ];

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

    $(document).ready(function() {
        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('get-table-nota-dinas') }}',
            columns: column,
        });

        var detailRows = [];
    
        function format(d) {
            let productsArray = JSON.parse(d.products);
            let productList = `
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc;">
                            <th style="padding: 8px; text-align: left;">Name</th>
                            <th style="padding: 8px; text-align: left;">Required</th>
                            <th style="padding: 8px; text-align: left;">Ready</th>
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
