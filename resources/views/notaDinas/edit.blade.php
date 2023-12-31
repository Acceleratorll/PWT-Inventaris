@extends('adminlte::page')

@section('title', 'Nota Dinas')

@section('content_header')
    <h1>Edit Nota Dinas Barang</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('notaDinas.update', ['notaDina' => $data->id]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-7">
                    <label for="code">Code</label>
                    <input type="text" name="code" id="code" class="form-control mb-3" placeholder="Code here~~" value="{{ $data->code }}" required>
                </div>
                <div class="col-md-2">
                    <label for="from_date">Dari</label>
                    <input type="date" name="from_date" id="from_date" value="{{ $data->from_date }}" class="form-control mb-3" required>
                </div>
                <div class="col-md-2">
                    <label for="to_date">Hingga</label>
                    <input type="date" name="to_date" id="to_date" value="{{ $data->to_date }}" class="form-control mb-3" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <label for="products" class="control-label">Barang (Bisa pilih lebih dari 1)</label>
                    <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple>
                        @foreach ($data->product_plannings as $proplan)
                            <option value="{{ $proplan->id }}" selected>{{ $proplan->product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="desc">Description</label>
                    <textarea name="desc" id="desc" class="form-control mb-3" placeholder="Description">{{ $proplan->desc }}</textarea>
                </div>
            </div>
            <div id="selected-products"></div>
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <button class="form-control btn btn-outline-success" type="submit">Save</button>
                </div>
            </div>
            </form>
        </div>
    </div>
@stop
    
    @section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    @stop
    
    @section('js')
    <script src="{{ asset('/js/customSelect2.js') }}"></script>
    <script src="{{ asset('/js/select2WithParam.js') }}"></script>
    <script>
        const products = document.getElementById("products");
        const products_ph = "Pilih Barang";
        const products_url = '{{ route("get-json-products") }}';

        const productsSelect = $("#products");
        const selectedProductsDiv = $("#selected-products");

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
                type: 'success',title: 'Success',
                timer: 3000,
                text: '{{ Session::get('success') }}',
                onOpen: function() {
                    Swal.showLoading()
                }
            });
        }

        $(document).ready(function() {
            selectInput(products, products_url, products_ph);
            updateSelectedProducts();
        });

        function updateSelectedProducts() {
            selectedProductsDiv.empty();
            const selectedProducts = productsSelect.select2("data");

            selectedProducts.forEach(function (product) {
                var productId = product.id;
                var productName = product.text;
                var uniqueLocationId = "location_id_" + productId;
                
                $.ajax({
                    url: '{{ route("productPlanning.show", ["productPlanning" => ":product"]) }}'.replace(':product', productId),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var inputHtml = `
                        <div class="row">
                            <div class="col-md-4">
                                <label>Product Name</label>
                                <input type="text" class="form-control mb-3" name="selected_products[${productId}][name][]" value="${productName} | ${data.data.product.product_code}" disabled>
                            </div>
                            <div class="col-md-2">
                                <label>Requirement</label>
                                <input type="number" class="form-control mb-3" name="selected_products[${productId}][requirement_amount]" value="${data.data.requirement_amount}" placeholder="Requirement amount" required>
                            </div>
                            <div class="col-md-2">
                                <label>Stock</label>
                                <input type="number" class="form-control mb-3" name="selected_products[${productId}][product_amount]" value="${data.data.product.total_amount}" required readonly>
                            </div>
                            <div class="col-md-2">
                                <label>Procurement</label>
                                <input type="number" class="form-control mb-3" name="selected_products[${productId}][procurement_plan_amount]" value="${data.data.procurement_plan_amount}" placeholder="Procurement plan amount" required>
                            </div>
                            <div class="col-md-2">
                                <label>Qualifier</label>
                                <input type="text" class="form-control mb-3" name="selected_products[${productId}][qualifier]" value="${data.data.product.qualifier.name}" required readonly>
                            </div>
                        </div>
                        <div id="locations_${productId}"></div>
                    `;
                    $("#selected-products").append(inputHtml);
                },
                error: function (error) {
                    console.error("Error fetching product data:", error);
                }
            });
            });
        }
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
@stop