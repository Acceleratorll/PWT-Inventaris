@extends('adminlte::page')

@section('title', 'Edit RPP')

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
        <form action="{{ route('rpp.update',['rpp' =>$rpp->id]) }}" method="post">
            @csrf
            @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="customer">Customer</label>
                    <input type="text" value="{{ $rpp->customer }}" class="form-control mb-3" name="customer" placeholder="Masukkan Nama Customer" required/>
                </div>
                <div class="col-md-6">
                    <label for="product_code">Kode RPP</label>
                    <input type="number" value="{{ $rpp->code }}" class="form-control mb-3" name="code" id="code" placeholder="Masukkan Kode RPP" required/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="order_type">Jenis Orderan</label>
                    <input name="order_type" value="{{ $rpp->order_type }}" id="order_type" class="form-control mb-3" placeholder="Masukkan Jenis Orderan" required/>
                </div>
                <div class="col-md-8">
                    <label for="products">Barang (Bisa pilih lebih dari 1)</label>
                    <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple>
                        @if($rpp->outgoing_products)
                        @foreach($rpp->outgoing_products as $outgoing_product)
                        <option value="{{ $outgoing_product->product_id }}" selected>{{ $outgoing_product->product->name }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div><br>
            <div id="selected-products">
            </div>
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
        $(document).ready(function () {
        const productsSelect = $("#products");
        const selectedProductsDiv = $("#selected-products");

        function getProductQty(productId) {
            @foreach($rpp->outgoing_products as $outgoing_product)
                if ({{ $outgoing_product->product_id }} == productId) {
                    return {{ $outgoing_product->qty }};
                }
            @endforeach
        }
        // Function to update selected products dynamically
        function updateSelectedProducts() {
            selectedProductsDiv.empty();
            const selectedProducts = productsSelect.select2("data");

            selectedProducts.forEach(function (product) {
                const productId = product.id;
                const productName = product.text;
                const productQty = getProductQty(productId);

                // Create input fields for each selected product
                const inputHtml = `
                    <div class="row justify-end">
                        <div class="col-md-4"></div>
                        <div class="col-md-2">
                            <label>Nama Barang</label>
                            <input type="hidden" name="selected_products[${productId}][product_id]" value="${productId}">
                            <input type="text" class="form-control mb-3" value="${productName}" disabled>
                        </div>
                        <div class="col-md-2">
                            <label>Qty</label>
                            <input type="number" name="selected_products[${productId}][qty]" class="form-control mb-3" value="${productQty}" placeholder="Quantity" required>
                        </div>
                    </div>
                `;

                selectedProductsDiv.append(inputHtml);
            });
        }

        // Function to get the quantity from outgoing_products

        // Initialize select2 and update selected products on change
        productsSelect.select2({
            width: "100%",
            multiple: true,
            placeholder: "Tambah Barang",
        }).on("change", function () {
            updateSelectedProducts();
        });

        // Initially, update selected products if there are any pre-selected
        updateSelectedProducts();
    });
    </script>
@stop