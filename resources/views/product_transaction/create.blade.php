@extends('adminlte::page')

@section('title', 'Tambah RPP')

@section('content_header')
    <h1>Tambah Barang</h1>
@stop

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
        <form action="{{ route('rpp.store') }}" method="post">
            @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="customer_id">Customer</label>
                    <select class="form-control mb-3" name="customer_id" id="customer_id" required></select>
                </div>
                <div class="col-md-6">
                    <label for="product_code">Kode RPP</label>
                    <input type="number" class="form-control mb-3" name="code" id="code" placeholder="Masukkan Kode RPP" required/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="order_type">Jenis Orderan</label>
                    <input name="order_type" id="order_type" class="form-control mb-3" placeholder="Masukkan Jenis Orderan" required/>
                </div>
                <div class="col-md-8">
                    <label for="products">Barang (Bisa pilih lebih dari 1)</label>
                    <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple></select>
                </div>
            </div><br>
                <div id="selected-products"></div>
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <button class="form-control btn btn-success" type="submit">Save</button>
                </div>
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
        const products = document.getElementById("products");
        const customers = document.getElementById("customer_id");
        const products_ph = "Pilih Barang";
        const products_url = '{{ route("get-json-products") }}';
        const customer_ph = "Pilih Customer";
        const customer_url = '{{ route("json-get-customers") }}';
        $(document).ready(function() {
            selectInput(products, products_url, products_ph);
            selectInput(customers, customer_url, customer_ph);
        });

        $(products).on("change", function () {
        var selectedProducts = $(this).select2("data");

        $("#selected-products").empty();

        selectedProducts.forEach(function (product) {
            var productId = product.id;
            var productName = product.text;
            $.ajax({
            url: '{{ route("get-json-qualifiers-by-product", ["product" => ":productId"]) }}'.replace(':productId', productId),
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                var qualifiers = data.qualifiers;
            var inputHtml = `
                <div class="row justify-end">
                    <div class="col-md-4"></div>
                    <div class="col-md-2">
                        <label>Nama Barang</label>
                        <input type="hidden" name="selected_products[${productId}][product_id]" value="${productId}">
                        <input type="text" class="form-control mb-3" value="${productName}" disabled>
                    </div>
                    <div class="col-md-2">
                        <label>QTY</label>
                        <input type="number" name="selected_products[${productId}][qty]" class="form-control mb-3" placeholder="Quantity" required>
                    </div>
                    <div class="col-md-2">
                        <label>Qualifier</label>
                        <select name="selected_products[${productId}][qualifier_id]" class="form-control mb-3" required>
                            ${qualifiers.map(q => `<option value="${q.id}" selected>${q.name}</option>`).join('')}
                        </select>
                    </div>
                </div>
                `;
            $("#selected-products").append(inputHtml);
        },

            error: function (error) {
                console.error('Error retrieving data for product:', error);
            }
        });
    });
});
    </script>
@stop