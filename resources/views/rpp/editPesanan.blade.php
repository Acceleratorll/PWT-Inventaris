@extends('adminlte::page')

@section('title', 'Alokasi Barang Keluar')

@section('content_header')
    <h1>Alokasi Barang Keluar</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <form action="/rpp/takeout" id="input-form" method="post">
            @csrf
            <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="customer_id">Pelanggan</label>
                    <select name="customer_id" id="customer_id" class="form-control" required disabled>
                        <option value="{{ $rpp->customer_id }}" selected>{{ $rpp->customer->name }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="product_code">Kode RPP</label>
                    <input type="text" value="{{ $rpp->code }}" class="form-control mb-3" name="code" id="code" placeholder="Masukkan Kode RPP" required readonly/>
                    <input type="hidden" value="{{ $rpp->id }}" class="form-control mb-3" name="rpp_id" id="rpp_id" required readonly/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label for="order_type">Jenis Orderan</label>
                    <select name="order_type_id" id="order_type_id" class="form-control mb-3" placeholder="Masukkan Jenis Orderan" required disabled>
                        <option value="{{ $rpp->order_type_id }}">{{ $rpp->order_type->name }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="outed_date">Tanggal</label>
                    <input type="datetime-local" value="{{ $rpp->outed_date }}" name="outed_date" id="outed_date" class="form-control" placeholder="Masukkan tanggal barang keluar" value="{{ now()->format('Y-m-d') }}" readonly/>
                </div>
                <div class="col-md-7">
                    <label for="products">Barang (Bisa pilih lebih dari 1)</label>
                    <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple readonly>
                        @foreach ($rpp->outgoing_products as $item)
                        <option value="{{ $item->product_id }}" selected>{{ $item->product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div><br>
                <div id="selected-products"></div>
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
    
    if ('{{ Session::has('error') }}') {
        Swal.fire({
            icon: 'error',
            type: 'error',
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

    $(document).ready(function () {
        const customer = document.getElementById("customer_id");
        const products = document.getElementById("products");
        const locations = document.getElementById("locations");
        const order_type = document.getElementById("order_type_id");
        const productsSelect = $("#products");
        const selectedProductsDiv = $("#selected-products");
        const order_type_ph = "Pilih Order Type";
        const order_type_url = '{{ route("select-order-type") }}';
        const products_ph = "Pilih Barang";
        const products_url = '{{ route("get-json-products") }}';
        const customer_ph = "Pilih Customer";
        const customer_url = '{{ route("get-json-customers") }}';
        const location_ph = "Pilih Location";
        const locations_url = '{{ route("select-product-locations-param") }}';
        
        let totalRealAmount=0;

        selectInput(customer, customer_url, customer_ph);
        selectInput(products, products_url, products_ph);
        selectInput(order_type, order_type_url, order_type_ph);
        
        function getProductQty(productId) {
            @foreach($rpp->outgoing_products as $outgoing_product)
                if ({{ $outgoing_product->product_id }} == productId) {
                    return {{ $outgoing_product->amount }};
                }
            @endforeach
        }

        function getLocation(productId) {
            @foreach($rpp->outgoing_products as $outgoing_product)
                if ({{ $outgoing_product->product_id }} == productId) {
                    return {{ $outgoing_product->amount }};
                }
            @endforeach
        }

        function getLocationDetails(locationId) {
            @foreach($rpp->outgoing_products as $proction)
                @foreach($proction->product->product_locations as $location)
                    @if ($location->location_id == '{{ locationId }}' && $rpp->outed_date == '{{ $location->outed_date }}')
                        return { expired: '{{ $location->expired }}', amount: {{ $location->amount }} };
                    @endif
                @endforeach
            @endforeach
            return { expired: '', amount: 0 };
        }

        function sumAmount(productId) {
            let totalAmount = 0;

            $(`[name^="selected_products[${productId}][pro_loc_ids]"][name$="[amount]"]`).each(function () {
                const amount = parseFloat($(this).val()) || 0;
                totalAmount += amount;
            });

            $(`[name="selected_products[${productId}][taken_amount]"]`).val(totalAmount);
        }

        const formatDate = (dateStr) => {
            const date = new Date(dateStr);
            return date.toISOString().split('T')[0]; // "yyyy-MM-dd" format
        };

        function updateSelectedProducts() {
            var selectedProducts = productsSelect.select2("data");

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
                            <div class="col-md-3">
                                <label>Lokasi</label>
                                <select name="selected_products[${productId}][location][]" id="${uniqueLocationId}" class="form-control mb-3" multiple required></select>
                            </div>
                                <div class="col-md-1">
                                    <label>Keluar</label>
                                    <input type="text" name="selected_products[${productId}][taken_amount]" class="form-control mb-3" placeholder="Amount" required readonly>
                                </div>
                            <div class="col-md-1">
                                    <label>Permintaan</label>
                                    <input type="text" name="selected_products[${productId}][real_amount]" value="${getProductQty(productId)}" class="real-amount form-control mb-3" placeholder="Amount" required readonly>
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
                                                <div class="col-md-6">
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
                                                    <input type="number" name="selected_products[${productId}][pro_loc_ids][${location.id}][product_Amount]" value="${data.data.amount}" class="form-control mb-3" required readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Kadaluarsa</label>
                                                    <input type="date" name="selected_products[${productId}][pro_loc_ids][${location.id}][expired]" value="${formatDate(data.data.expired)}" class="form-control mb-3" required readonly>
                                                    <input type="hidden" name="selected_products[${productId}][pro_loc_ids][${location.id}][purchase_date]" value="${formatDate(data.data.purchase_date)}" class="form-control mb-3" required>
                                                </div>
                                            </div>
                                    `;
            
                                    $(`#locations_${productId}`).append(inputFields);

                                    $(`[name^="selected_products[${productId}][pro_loc_ids]"][name$="[amount]"]`).on('input', function () {
                                        sumAmount(productId);
                                    });
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
