@extends('adminlte::page')

@section('title', 'Edit RPP')

@section('content_header')
    <h1>Tambah Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <form action="{{ route('rpp.update',['rpp' =>$rpp->id]) }}" method="post">
            @csrf
            @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="customer_id">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control" required>
                        <option value="{{ $rpp->customer_id }}">{{ $rpp->customer->name }}</option>
                    </select>
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
                        <option value="{{ $outgoing_product->product_id }}" selected>{{ $outgoing_product->product->name ?? 'Product Deleted' }}</option>
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

        const customer = document.getElementById("customer_id");
        const products = document.getElementById("products");
        const products_ph = "Pilih Barang";
        const products_url = '{{ route("get-json-products") }}';
        const customer_ph = "Pilih Customer";
        const customer_url = '{{ route("get-json-customers") }}';
        
        selectInput(customer, customer_url, customer_ph);
        selectInput(products, products_url, products_ph);

        function getProductQty(productId) {
            @foreach($rpp->outgoing_products as $outgoing_product)
                if ({{ $outgoing_product->product_id }} == productId) {
                    return {{ $outgoing_product->qty }};
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
                            <div class="row justify-end">
                                <div class="col-md-4"></div>
                                <div class="col-md-2">
                                    <label>Nama Barang</label>
                                    <input type="hidden" name="selected_products[${productId}][product_id]" value="${productId}">
                                    <input type="text" class="form-control mb-3" value="${productName}" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label>Qty</label>
                                    <input type="number" name="selected_products[${productId}][qty]" value="${getProductQty(productId)}" class="form-control mb-3" placeholder="Quantity" required>
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
            // console.log(productsSelect.select2("data"));
            updateSelectedProducts();
        });

        updateSelectedProducts();
    });
</script>

@stop