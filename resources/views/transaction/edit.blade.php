@extends('adminlte::page')

@section('title', 'Edit transaction')

@section('content_header')
    <h1>Edit Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        @if($message = Session::get('info'))
            <x-adminlte-alert theme="info" title="Info">
                {{ $message }}
            </x-adminlte-alert>
        @elseif($message =  Session::get('success'))
            <x-adminlte-alert theme="success" title="Success">
                {{ $message }}
            </x-adminlte-alert>
        @elseif($message =  Session::get('warning'))
            <x-adminlte-alert theme="warning" title="Warning">
                {{ $message }}
            </x-adminlte-alert>
        @elseif($message =  Session::get('error'))
            <x-adminlte-alert theme="danger" title="Danger">
                {{ $message }}
            </x-adminlte-alert>
        @endif
    </div>
</div>
<div class="row">
    <div class="card col-md-12">
        <form action="{{ route('transaction.update',['transaction' =>$transaction->id]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="supplier_id">Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-control" required>
                            <option value="{{ $transaction->supplier_id }}">{{ $transaction->supplier->name }}</option>
                        </select>
                    </div>
                        <div class="col-md-6">
                        <label for="code">Kode</label>
                        <input type="number" value="{{ $transaction->code }}" class="form-control mb-3" name="code" id="code" placeholder="Masukkan Kode transaction" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="purchase_date">Tanggal Beli</label>
                        <input type="datetime-local" name="purchase_date" value="{{ $transaction->purchase_date }}" id="purchase_date" class="form-control mb-3" placeholder="Masukkan Tanggal Pembelian" required/>
                    </div>
                    <div class="col-md-8">
                        <label for="products">Barang (Bisa pilih lebih dari 1)</label>
                        <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple>
                            @if($transaction->product_transactions)
                                @foreach($transaction->product_transactions as $product_transaction)
                                    <option value="{{ $product_transaction->product_id }}" selected>{{ $product_transaction->product->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div><br>
                <div id="selected-products">
                </div>
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
        const supplier = document.getElementById("supplier_id");
        const products = document.getElementById("products");
        const products_ph = "Pilih Barang";
        const products_url = '{{ route("get-json-products") }}';
        const supplier_ph = "Pilih Supplier";
        const supplier_url = '{{ route("get-json-suppliers") }}';
        $(document).ready(function() {
            selectInput(supplier, supplier_url, supplier_ph);
            selectInput(products, products_url, products_ph);
        });

        // console.log(select2());
         function getProductQty(productId) {
            @foreach($transaction->product_transactions as $product_transaction)
                if ({{ $product_transaction->product_id }} == productId) {
                    return {{ $product_transaction->amount }};
                }
            @endforeach
        }

        function getLocation(productId) {
            @foreach($transaction->product_transactions as $product_transaction)
                if ({{ $product_transaction->product_id }} == productId) {
                    return {{ $product_transaction->amount }};
                }
            @endforeach
        }
            
        const productsSelect = $("#products");
        const selectedProductsDiv = $("#selected-products");
        
        function updateSelectedProducts() {
            selectedProductsDiv.empty();
            const selectedProducts = productsSelect.select2("data");

            selectedProducts.forEach(function (product) {
                const productId = product.id;
                const productName = product.text;

                console.log(product);

                $.ajax({
                    url: `{{ route("get-json-product", ["product_id" => ":product"]) }}}`.replace(':product', productId),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        const qualifier = data.qualifier;

                        const inputHtml = `
                            <div class="row justify-center">
                                <div class="col-md-3">
                                    <label>Nama Barang</label>
                                    <input type="hidden" name="selected_products[${productId}][product_id]" value="${productId}">
                                    <input type="text" class="form-control mb-3" value="${productName}" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label>Qty</label>
                                    <input type="number" name="selected_products[${productId}][qty]" value="${getProductQty(productId)}" class="form-control mb-3" placeholder="Quantity" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Expired</label>
                                    <input type="date" name="selected_products[${productId}][expired]" id="expired" class="form-control mb-3" required/>
                                </div>
                        <div class="col-md-2">
                                    <label>Location</label>
                                    <select name="selected_products[${productId}][location_id]" id="${uniqueLocationId}" class="form-control mb-3" required></select>
                                </div>
                                <div class="col-md-2">
                                    <label>Qualifier</label>
                                    <input type="text" name="selected_products[${productId}][qualifier_id]" class="form-control mb-3" value="${qualifier.name}" placeholder="Qualifier" required>
                                </div>
                            </div>
                        `;
                        selectedProductsDiv.append(inputHtml);
                    },
                    error: function (error) {
                        console.error("Error fetching qualifier data:", error);
                    }
                });
            });
        }

        productsSelect.select2({
            width: "100%",
            multiple: true,
            placeholder: "Tambah Barang",
        }).on("change", function () {
            updateSelectedProducts();
        });

        updateSelectedProducts();
    });
</script>
@stop
