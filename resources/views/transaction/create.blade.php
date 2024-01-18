@extends('adminlte::page')

@section('title', 'Tambah Transaksi Barang')

@section('content_header')
    <h1>Tambah Transaksi Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <form action="{{ route('transaction.store') }}" method="post">
            @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="supplier_id">Supplier</label>
                    <select class="form-control mb-3" name="supplier_id" id="supplier_id" required></select>
                </div>
                <div class="col-md-6">
                    <label for="product_code">Kode</label>
                    <input type="text" class="form-control mb-3" name="code" id="code" placeholder="Masukkan Kode Transaksi" required/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="purchase_date">Tanggal Beli</label>
                    <input name="purchase_date" type="datetime-local" id="purchase_date" class="form-control mb-3" placeholder="Masukkan Tanggal Pembelian" value="{{ now() }}" required/>
                </div>
                <div class="col-md-8">
                    <label for="products">Barang (Bisa pilih lebih dari 1)</label>
                    <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple></select>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-8">
                    <label for="note">Catatan</label>
                    <textarea name="note"class="form-control mb-3" placeholder="Masukkan Catatan Pembelian"></textarea>
                </div>
            </div>
        </div>
        <div id="selected-products"></div>
        <div class="row justify-content-end">
            <div class="col-md-3">
                <button class="form-control btn-success" type="submit">Simpan</button>
            </div>
        </div>
    </form>
    </div>
</div>
@stop

@section('css')
    <style>
        .select2-selection__choice {
            padding-right: 16px !important;
            padding-left: 22px !important;
            background-color: ghostwhite !important;
            color: #333 !important;
        }
    </style>
@stop
    
@section('js')
    <script src="{{ asset('/js/customSelect2.js') }}"></script>
    <script>
        if ('{{ Session::has('error') }}') {
            Swal.fire({
                icon: 'error',
                type: 'error',
                timer: 3000,
                title: 'Error',
                text: '{{ Session::get('error') }}',
            });
        }
    
        if ('{{ Session::has('success') }}') {
            Swal.fire({
                icon: 'success',
                type: 'success',
                title: 'Success',
                timer: 3000,
                text: '{{ Session::get('success') }}',
            });
        }
            
        const products = document.getElementById("products");
        const locations = document.getElementById("locations");
        const suppliers = document.getElementById("supplier_id");
        const products_ph = "Pilih Barang";
        const products_url = '{{ route("get-json-products") }}';
        const location_ph = "Pilih Location";
        const locations_url = '{{ route("get-json-locations") }}';
        const supplier_ph = "Pilih Supplier";
        const supplier_url = '{{ route("get-json-suppliers") }}';
        $(document).ready(function() {
            selectInput(products, products_url, products_ph);
            selectInput(suppliers, supplier_url, supplier_ph);
        });

        $(products).on("change", function () {
            var selectedProducts = $(this).select2("data");

            $("#selected-products").empty();

            selectedProducts.forEach(function (product) {
                var productId = product.id;
                var productName = product.text;
                $.ajax({
                    url: '{{ route("get-json-product", ["product_id" => ":product"]) }}'.replace(':product', productId),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var qualifiers = data.qualifiers;
                        var uniqueLocationId = "location_id_" + productId;
                        var inputHtml = `
                            <div class="row justify-center">
                                <div class="col-md-4">
                                    <label>Nama Barang</label>
                                    <input type="hidden" name="selected_products[${productId}][product_id]" value="${productId}">
                                    <input type="text" class="form-control mb-3" value="${productName}" disabled>
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-3">
                                    <label>Jumlah</label>
                                    <input type="number" class="form-control mb-3" name="selected_products[${productId}][amount]" placeholder="Amount" required>
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-2">
                                    <label>Satuan</label>
                                    <select name="selected_products[${productId}][qualifier_id]" class="form-control mb-3" required>
                                        <option value="${data.qualifier_id}" selected>${data.qualifier.name}</option>
                                    </select>
                                </div>
                            </div>
                            <div id="locations_${productId}"></div>
                            `;
                        
                        $("#selected-products").append(inputHtml);
                        selectInput($("#" + uniqueLocationId), locations_url, location_ph);

                        $(`#${uniqueLocationId}`).on('change', function() {
                            const selectedLocations = $(this).select2("data");
                            $(`#locations_${productId}`).empty();
                            
                            selectedLocations.forEach(location => {
                                const inputFields = `
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <label>Lokasi</label>
                                                <input type="text" name="selected_products[${productId}][location_ids][${location.id}][name]" class="form-control mb-3" value="${location.text}" placeholder="Amount">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Jumlah</label>
                                                <input type="number" name="selected_products[${productId}][location_ids][${location.id}][amount]" class="form-control mb-3" placeholder="Amount" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Kadaluarsa</label>
                                                <input type="date" name="selected_products[${productId}][location_ids][${location.id}][expired]" class="form-control mb-3" required>
                                            </div>
                                        </div>
                                `;

                                $(`#locations_${productId}`).append(inputFields);
                            });
                        });
                    },
                    error: function (error) {
                        console.error('Error retrieving data for product:', error);
                    }
                });
            });
        });
    </script>
@stop