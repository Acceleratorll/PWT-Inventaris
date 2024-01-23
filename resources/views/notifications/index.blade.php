@extends('adminlte::page')

@section('title', 'Notification')

@section('content_header')
    <h1>Notification</h1>
@stop

@section('content')
    <div class="row">
        <div class="card col-md-12">
            <div class="card-body">
                @if (auth()->user()->unreadNotifications->isEmpty())
                <p>No unread notifications available.</p>
                @else
                <a href="{{ route('read-all') }}" class="btn btn-outline-primary">Mark All as Read</a>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <caption>Tabel Notifications</caption>
                        <thead class="thead-light">
                            <tr>
                                <th scope="col" class="text-center">Nama</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Stock</th>
                                <th scope="col" class="text-center">Updated</th>
                                <th scope="col" class="text-center">Created</th>
                                <th scope="col" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <h1>History</h1>
            </div>
        </div>
        <div class="card col-md-12">
            <div class="card-body">
                @if (auth()->user()->readNotifications->isEmpty())
                <p>No history notifications available.</p>
                @else
                <div class="table-responsive">
                    <table class="table  table-bordered" id="table-history">
                        <caption>History Notifications</caption>
                        <thead class="thead-light">
                            <tr>
                                <th scope="col" class="text-center">Nama</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Stock</th>
                                <th scope="col" class="text-center">Updated</th>
                                <th scope="col" class="text-center">Created</th>
                            </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
    

@section('css')
@stop
    
@section('js')
<script>
    if ('{{ Session::has('error') }}') {
        Swal.fire({
            icon: 'error',
            type: 'error',
            title: 'Error',
            timer: 3000,
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
            timer: 3000,
            text: '{{ Session::get('success') }}',
            onOpen: function() {
                Swal.showLoading()
            }
        });
    }

    $('#table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('get-table.unread-notifications') }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'type', name: 'type' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'formatted_updated_at', name: 'formatted_updated_at' },
            { data: 'formatted_created_at', name: 'formatted_created_at' },
            { data: 'action', name: 'action', orderable: false },
        ],
    });

    $('#table-history').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('get-table.read-notifications') }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'type', name: 'type' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'formatted_updated_at', name: 'formatted_updated_at' },
            { data: 'formatted_created_at', name: 'formatted_created_at' },
        ],
    });
</script>
@stop