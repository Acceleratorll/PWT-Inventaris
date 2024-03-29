@extends('adminlte::page')

@section('title', 'Tambah RPP')

@section('content_header')
    <h1>Add RPP</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <form action="{{ route('rpp.store') }}" id="input-form" method="post">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="customer_id">Pelanggan</label>
                    <select name="customer_id" id="customer_id" class="form-control" required></select>
                </div>
                <div class="col-md-6">
                    <label for="product_code">Kode RPP</label>
                    <input type="text" class="form-control mb-3" name="code" id="code" placeholder="Masukkan Kode RPP" required/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label for="order_type">Jenis Orderan</label>
                    <select name="order_type_id" id="order_type_id" class="form-control mb-3" placeholder="Masukkan Jenis Orderan" required></select>
                </div>
                <div class="col-md-2">
                    <label for="outed_date">Tanggal</label>
                    <input type="date" name="outed_date" id="outed_date" class="form-control" placeholder="Masukkan tanggal barang keluar" value="{{ now()->format('Y-m-d') }}"/>
                </div>
                <div class="col-md-7">
                    <label for="products">Barang (Bisa pilih lebih dari 1)</label>
                    <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple></select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="note">Deskripsi</label>
                    <textarea name="desc"class="form-control mb-3" placeholder="Masukkan Catatan Pembelian"></textarea>
                </div>
            </div>
            <br><div id="selected-products"></div>
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <button class="form-control btn btn-outline-success" type="button" onclick="confirmation()">Simpan</button>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>

@stop
@section('plugins.Select2', true)

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
<script src="{{ asset('/js/select2WithParam.js') }}"></script>
<script>
    const customer = document.getElementById("customer_id");
    const products = document.getElementById("products");
    const order_type = document.getElementById("order_type_id");
    const products_ph = "Pilih Barang";
    const products_url = '{{ route("get-json-products") }}';
    const customer_ph = "Pilih Customer";
    const customer_url = '{{ route("get-json-customers") }}';
    const order_type_ph = "Pilih Order Type";
    const order_type_url = '{{ route("select-order-type") }}';
    const location_ph = "Pilih Location";
    const locations_url = '{{ route("select-product-locations-param") }}';
    
    jQuery(document).ready(function() {
        selectInput(products, products_url, products_ph);
        selectInput(customer, customer_url, customer_ph);
        selectInput(order_type, order_type_url, order_type_ph);
    });

    const formatDate = (dateStr) => {
        const date = new Date(dateStr);
        return date.toISOString().split('T')[0]; // "yyyy-MM-dd" format
    };

    $(products).on("change", function () {
        var selectedProducts = $(this).select2("data");

        $("#selected-products").empty();

        selectedProducts.forEach(function (product) {
            var productId = product.id;
            var productName = product.text;
            var uniqueLocationId = "location_id_" + productId;
            
            $.ajax({
                url: '{{ route("get-json-product", ["product_id" => ":product"]) }}'.replace(':product', productId),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    var inputHtml = `
                    <div class="row">
                        <div class="col-md-5">
                            <label>Nama Barang</label>
                            <input type="hidden" name="selected_products[${productId}][product_id]" value="${productId}">
                            <input type="text" class="form-control mb-3" value="${productName}" disabled>
                        </div>
                        <div class="col-md-5">
                            <label>Lokasi</label>
                            <select name="selected_products[${productId}][location][]" id="${uniqueLocationId}" class="form-control mb-3" multiple required></select>
                        </div>
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
                selectInputWithParam($("#" + uniqueLocationId), locations_url, location_ph, productId);
                $(`#${uniqueLocationId}`).on('change', function() {
                    const selectedLocations = $(this).select2("data");
                    $(`#locations_${productId}`).empty();
                    
                    selectedLocations.forEach(location => {
                        $.ajax({
                            url: '{{ route("productLocation.show", ["productLocation" => ":product"]) }}'.replace(':product', location.id),
                            type: 'GET',
                            dataType: 'json',
                            success: function (data) {
                                const inputFields = `
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Lokasi</label>
                                                <input type="text" name="selected_products[${productId}][pro_loc_ids][${location.id}][name]" class="form-control mb-3" value="${data.data.location.name}" placeholder="Amount">
                                                <input type="hidden" name="selected_products[${productId}][pro_loc_ids][${location.id}][location_id]" class="form-control mb-3" value="${data.data.location_id}">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Jumlah</label>
                                                <input type="number" name="selected_products[${productId}][pro_loc_ids][${location.id}][amount]" class="form-control mb-3" placeholder="Amount" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Saldo</label>
                                                <input type="number" name="selected_products[${productId}][pro_loc_ids][${location.id}][oriAmount]" value="${data.data.amount}" class="form-control mb-3" required readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Kadaluarsa</label>
                                                <input type="date" name="selected_products[${productId}][pro_loc_ids][${location.id}][expired]" value="${formatDate(data.data.expired)}" class="form-control mb-3" required readonly>
                                                <input type="hidden" name="selected_products[${productId}][pro_loc_ids][${location.id}][purchase_date]" value="${formatDate(data.data.purchase_date)}" class="form-control mb-3" required>
                                            </div>
                                        </div>
                                `;
        
                                $(`#locations_${productId}`).append(inputFields);
                            },
                            error: function (error) {
                                console.error("Error fetching product location data:", error);
                            }
                        });
                    });
                });
            },
            error: function (error) {
                console.error("Error fetching product data:", error);
            }
        });
    });
});

function confirmation() {
    Swal.fire({
        title: 'Apakah anda sudah yakin ?',
        text: 'Apakah anda sudah yakin dengan inputan anda ?',
        type: 'warning',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            $('#input-form').submit();
        }
    });
}
</script>
@stop