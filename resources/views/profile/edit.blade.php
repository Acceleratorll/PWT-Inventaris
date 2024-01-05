@extends('adminlte::page')

@section('title', 'Edit Profile Page')

@section('content-header')
    <h1>Edit Profile</h1>
@endsection

@section('content')
<div class="row">
    <div class="card col-md-12">
        <form action="{{ route('profile.update', ['profile' => $user->id]) }}" method="post">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control mb-3" value="{{ $user->name }}" name="name" label="Nama" placeholder="Masukkan Nama" required/>
                </div>
                <div class="col-md-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control mb-3" value="{{ $user->email }}" name="email" label="Email" id="email" placeholder="Masukkan Email" required/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="role_id">Roles</label>
                    <select name="role_id" id="role_id" class="form-control mb-3" width="100%" required>
                        @if($user->role_id)
                        <option value="{{ $user->role_id }}" selected>{{ $user->role->name }}</option>
                        @else
                        <option value="" selected disabled>Pilih Roles</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" class="form-control mb-3" width="100%" autocomplete="new-password"/>
                </div>
            </div>
            <br>
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <button class="form-control btn btn-outline-success" type="submit">Save</button>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
@endsection


@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
@stop
    
@section('js')
    <script src="{{ asset('js/customSelect2.js') }}"></script>
    <script>
        const role = document.getElementById("role_id");
        const role_url = '/json/get-roles';
        $(document).ready(function() {
            selectInput(role, role_url);
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop