@extends('adminlte::page')

@section('title', 'Pengambilan Barang')

@section('content_header')
    <h1>Pengambilan Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <form action="{{ route('productLocation.store') }}" id="input-form" method="post">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="customer_id">Pelanggan</label>
                        <select name="customer_id" id="customer_id" class="form-control" required disabled>
                            <option value="{{ $rpp->customer_id }}">{{ $rpp->customer->name }}</option>
                        </select>
                    </div>
                        <div class="col-md-6">
                        <label for="code">Kode</label>
                        <input type="text" value="{{ $rpp->code }}" class="form-control mb-3" name="code" id="code" placeholder="Masukkan Kode rpp" required readonly/>
                        <input type="hidden" value="{{ $rpp->id }}" class="form-control mb-3" name="rpp_id" id="rpp_id" required readonly/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="outed_date">Tanggal Beli</label>
                        <input type="datetime-local" name="outed_date" value="{{ $rpp->outed_date }}" id="outed_date" class="form-control mb-3" placeholder="Masukkan Tanggal Pembelian" required readonly/>
                    </div>
                    <div class="col-md-8">
                        <label for="products">Barang (Bisa pilih lebih dari 1)</label>
                        <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple disabled>
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
<script>
    
    if ('{{ Session::has('error') }}') {
        Swal.fire({
            icon: 'error',
            type: 'error',
            title: 'Error',
            timer: 3000,
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
        const productsSelect = $("#products");
        const selectedProductsDiv = $("#selected-products");
        const products_ph = "Pilih Barang";
        const products_url = '{{ route("get-json-products") }}';
        const customer_ph = "Pilih Customer";
        const customer_url = '{{ route("get-json-customers") }}';
        const location_ph = "Pilih Location";
        const locations_url = '{{ route("get-json-locations") }}';
        
        let totalRealAmount=0;

        selectInput(customer, customer_url, customer_ph);
        selectInput(products, products_url, products_ph);
        
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

        function sumAmount(productId, locationId) {
            let totalAmount = 0;
            
            $(`[name^="selected_products[${productId}][location_ids]"][name$="[amount]"]`).each(function () {
                const amount = parseFloat($(this).val()) || 0;
                totalAmount += amount;
                console.log(totalAmount);
            });
            $(`[name="selected_products[${productId}][real_amounts]"]`).val(totalAmount);
        }

        function updateSelectedProducts() {
            selectedProductsDiv.empty();
            const selectedProducts = productsSelect.select2("data");

            selectedProducts.forEach(function (product) {
                const productId = product.id;
                const productName = product.text;

                $.ajax({
                    url: `{{ route("get-json-product", ["product_id" => ":product"]) }}}`.replace(':product', productId),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        const qualifier = data.qualifier;
                        var uniqueLocationId = "location_id_" + productId;

                        const inputHtml = `
                            <div class="row justify-center">
                                <div class="col-md-4">
                                    <label>Nama Barang</label>
                                    <input type="hidden" name="selected_products[${productId}][product_id]" value="${productId}">
                                    <input type="text" class="form-control mb-3" value="${productName}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label>Lokasi</label>
                                    <select name="selected_products[${productId}][location][]" id="${uniqueLocationId}" class="form-control mb-3" multiple required></select>
                                </div>
                                <div class="col-md-1">
                                    <label>Saldo</label>
                                    <input type="text" name="selected_products[${productId}][real_amounts]" class="real-amount form-control mb-3" placeholder="Amount" required readonly>
                                </div>
                                <div class="col-md-1">
                                    <label>Saldo Beli</label>
                                    <input type="text" name="selected_products[${productId}][order_amount]" class="form-control mb-3" value="${getProductQty(productId)}" placeholder="Amount" required readonly>
                                </div>
                                <div class="col-md-2">
                                    <label>Satuan</label>
                                    <input type="text" name="selected_products[${productId}][qualifier_id]" class="form-control mb-3" value="${qualifier.name}" placeholder="Qualifier" required readonly>
                                </div>
                            </div>
                            <div id="locations_${productId}"></div>
                        `;

                        selectedProductsDiv.append(inputHtml);
                        selectInput($("#" + uniqueLocationId), locations_url, location_ph);

                        const selectedLocations = $(`#${uniqueLocationId}`).select2("data");
                        console.log(selectedLocations);
                        $(`#locations_${productId}`).empty();

                        $(`#${uniqueLocationId}`).on('change', function() {
                            const selectedLocations = $(this).select2("data");
                            $(`#locations_${productId}`).empty();
                            console.log(selectedLocations);

                            selectedLocations.forEach(location => {
                                const locationId = location.id;
                                const locationDetails = getLocationDetails(locationId);
                                
                                const inputFields = `
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <label>Nama Lokasi</label>
                                            <input type="text" class="form-control mb-3" value="${location.text}" disabled>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Kadaluarsa</label>
                                            <input type="date" name="selected_products[${productId}][location_ids][${location.id}][expired]" class="form-control mb-3" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Jumlah</label>
                                            <input type="number" name="selected_products[${productId}][location_ids][${location.id}][amount]" class="form-control mb-3" placeholder="Amount" required>
                                        </div>
                                    </div>
                                `;

                                $(`#locations_${productId}`).append(inputFields);
                                
                                $(`[name^="selected_products[${productId}][location_ids][${locationId}][amount]"]`).on('input', function(){
                                    console.log('Inputan: ',$(this).val());
                                    sumAmount(productId, location.id);
                                    
                                    selectedLocations.forEach(location => {
                                        const locationId = location.id;

                                        $(`[name^="selected_products[${productId}][location_ids][${locationId}][amount]"]`).on('input', function(){
                                            sumAmount(productId, location.id);
                                        })
                                    });
                                })
                            });
                        });
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
