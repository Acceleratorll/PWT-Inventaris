@extends('adminlte::page')

@section('title', 'Tambah Barang')

@section('content_header')
    <h1>Tambah Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <form action="{{ route('product.store') }}" method="post">
            @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control mb-3" name="name" label="Nama Barang" placeholder="Masukkan Nama Produk" required/>
                </div>
                <div class="col-md-6">
                    <label for="product_code">Kode Barang</label>
                    <input type="text" class="form-control mb-3" name="product_code" label="Kode Barang" id="product_code" placeholder="Masukkan Kode Barang" required/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="material_id">Material</label>
                    <select name="material_id" id="material_id" class="form-control mb-3" width="100%" required>
                        <option value="" selected disabled>Pilih Material</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="product_type_id">Tipe Barang</label>
                    <select name="product_type_id" id="product_type_id" class="form-control mb-3" width="100%" required>
                        <option value="" selected disabled>Pilih Tipe Barang</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="category_product_id">Kategori Barang</label>
                    <select name="category_product_id" id="category_product_id" class="form-control mb-3" width="100%" required>
                        <option value="" selected disabled>Pilih Kategori Barang</option>
                    </select>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-6">
                    <label for="note">Keterangan</label>
                    <textarea name="note" id="note" cols="30" rows="10" placeholder="Keterangan" class="form-control" style="height: 25%"></textarea>
                </div>
                <div class="col-md-2">
                    <label for="max_amount">Minimal Stock</label>
                    <input type="number" class="form-control mb-3" name="minimal_amount" label="Minimal Stock" id="minimal_amount" placeholder="Masukkan Minimal Stock Produk" required/>
                </div>
                <div class="col-md-2">
                    <label for="max_amount">Stock Asli</label>
                    <input type="number" class="form-control mb-3" name="total_amount" id="total_amount" placeholder="Masukkan Stock Barang Asli" required/>
                </div>
                <div class="col-md-2">
                    <label for="qualifier_id">Satuan</label>
                    <select name="qualifier_id" id="qualifier_id" class="form-control mb-3 select2" required>
                        <option value="" selected disabled>Pilih Satuan Barang</option>
                    </select>
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

@stop
    
    @section('css')
    @stop
    
    @section('js')
    <script src="{{ asset('/js/customSelect2.js') }}"></script>
    <script>
        const material = document.getElementById("material_id");
        const product_type_id = document.getElementById("product_type_id");
        const qualifier = document.getElementById("qualifier_id");
        const category_product = document.getElementById("category_product_id");
        const qualifier_url = '/json/get-qualifiers';
        const product_type_url = '{{ route('select-product-type') }}';
        const material_url = '{{ route('select-material') }}';
        const category_product_url = '/json/get-categories';
        $(document).ready(function() {
            selectInput(qualifier, qualifier_url);
            selectInput(product_type_id, product_type_url);
            selectInput(material, material_url);
            selectInput(category_product, category_product_url);
        });
    </script>
@stop