@extends('adminlte::page')

@section('title', 'Nota Dinas')

@section('content_header')
    <h1>Nota Dinas Barang</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('notaDinas.store') }}" id="input-form" method="post">
            @csrf
            <div class="row">
                <div class="col-md-7">
                    <label for="code">Kode</label>
                    <input type="text" name="code" id="code" class="form-control mb-3" placeholder="Code here~~" required>
                </div>
                <div class="col-md-2">
                    <label for="from_date">Dari</label>
                    <input type="date" name="from_date" id="from_date" value="{{ now()->format('Y-m-d') }}" class="form-control mb-3" required>
                </div>
                <div class="col-md-2">
                    <label for="to_date">Hingga</label>
                    <input type="date" name="to_date" id="to_date" value="{{ now()->addMonth(5)->format('Y-m-d') }}" class="form-control mb-3" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <label for="products" class="control-label">Barang (Bisa pilih lebih dari 1)</label>
                    <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple></select>
                </div>
                <div class="col-md-5">
                    <label for="desc">Deskripsi</label>
                    <textarea name="desc" id="desc" class="form-control mb-3" placeholder="Description"></textarea>
                </div>
            </div>
            <div id="selected-products"></div>
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <button class="form-control btn btn-outline-success" type="button" onclick="confirmation()">Simpan</button>
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

            $(document).on("input", 'input[name^="selected_products["][name$="[requirement_amount]"]', function () {
                var requirementInput = $(this);
                var productId = requirementInput.closest(".row").find('input[name$="[product_amount]"]').val();
                var minimumStock = requirementInput.closest(".row").find('input[name$="[minimum_stock]"]').val();
                var procurementInput = requirementInput.closest(".row").find('input[name$="[procurement_plan_amount]"]');

                var requirement = parseFloat(requirementInput.val()) || 0;
                var stock = parseFloat(productId) || 0;
                var minimumStockValue = parseFloat(minimumStock) || 0;

                var procurement = Math.max(requirement - stock + minimumStockValue, 0);
                
                procurementInput.val(procurement.toFixed(2));
            });
        });
    
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
                            <div class="col-md-4">
                                <label>Product Name</label>
                                <input type="text" class="form-control mb-3" name="selected_products[${productId}][name][]" value="${productName} | ${data.product_code}" disabled>
                            </div>
                            <div class="col-md-2">
                                <label>Requirement</label>
                                <input type="number" class="form-control mb-3" name="selected_products[${productId}][requirement_amount]" placeholder="Requirement amount" required>
                            </div>
                            <div class="col-md-2">
                                <label>Stock</label>
                                <input type="number" class="form-control mb-3" name="selected_products[${productId}][product_amount]" value="${data.total_amount}" required readonly>
                                <input type="hidden" class="form-control mb-3" name="selected_products[${productId}][minimum_stock]" value="${data.minimal_amount}">
                            </div>
                            <div class="col-md-2">
                                <label>Procurement</label>
                                <input type="number" class="form-control mb-3" name="selected_products[${productId}][procurement_plan_amount]" placeholder="Procurement plan amount" required>
                            </div>
                            <div class="col-md-2">
                                <label>Satuan</label>
                                <input type="text" class="form-control mb-3" name="selected_products[${productId}][qualifier]" value="${data.qualifier.name}" required readonly>
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