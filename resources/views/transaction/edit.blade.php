@extends('adminlte::page')

@section('title', 'Edit transaction')

@section('content_header')
    <h1>Edit Transaksi</h1>
@stop

@section('content')
<div class="row">
    <div class="card col-md-12">
        <form action="{{ route('transaction.update',['transaction' =>$transaction->id]) }}" id="input-form" method="post">
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
                        <input type="text" value="{{ $transaction->code }}" class="form-control mb-3" name="code" id="code" placeholder="Masukkan Kode transaction" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="purchase_date">Tanggal Beli</label>
                        <input type="datetime-local" name="purchase_date" value="{{ $transaction->purchase_date }}" id="purchase_date" class="form-control mb-3" placeholder="Masukkan Tanggal Pembelian" required/>
                    </div>
                    <div class="col-md-8">
                        <label for="products">Barang (Bisa pilih lebih dari 1)</label>
                        <select name="products[]" id="products" class="form-control mb-3" width="100%" required multiple readonly>
                            @if($transaction->product_transactions)
                                @foreach($transaction->product_transactions as $product_transaction)
                                    <option value="{{ $product_transaction->product_id }}" selected>{{ $product_transaction->product->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label for="note">Catatan</label>
                        <textarea name="note"class="form-control mb-3" placeholder="Masukkan Catatan Pembelian">{{ $transaction->note }}</textarea>
                    </div>
                </div>
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
            title: 'Error',timer: 3000,
            text: '{{ Session::get('error') }}',
            
        });
    }

    if ('{{ Session::has('success') }}') {
        Swal.fire({
            icon: 'success',
            type: 'success',title: 'Success',
            timer: 3000,
            text: '{{ Session::get('success') }}',
            
        });
    }
    $(document).ready(function () {
        const supplier = document.getElementById("supplier_id");
        const products = document.getElementById("products");
        const locations = document.getElementById("locations");
        const products_ph = "Pilih Barang";
        const products_url = '{{ route("get-json-products") }}';
        const supplier_ph = "Pilih Supplier";
        const supplier_url = '{{ route("get-json-suppliers") }}';
        const location_ph = "Pilih Location";
        const locations_url = '{{ route("get-json-locations") }}';
        selectInput(supplier, supplier_url, supplier_ph);
        // selectInput(products, products_url, products_ph);
        
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

        function getLocationDetails(locationId) {
            @foreach($transaction->product_transactions as $proction)
                @foreach($proction->product->product_locations as $location)
                    @if ($location->location_id == '{{ locationId }}' && $transaction->purchase_date == '{{ $location->purchase_date }}')
    return { expired: '{{ $location->expired }}', amount: {{ $location->amount }} };
@endif
                @endforeach
            @endforeach
            return { expired: '', amount: 0 };
        }

        // const productsSelect = $("#products");
        // const selectedProductsDiv = $("#selected-products");
        
        // function updateSelectedProducts() {
        //     selectedProductsDiv.empty();
        //     const selectedProducts = productsSelect.select2("data");

        //     selectedProducts.forEach(function (product) {
        //         const productId = product.id;
        //         const productName = product.text;

        //         $.ajax({
        //             url: `{{ route("get-json-product", ["product_id" => ":product"]) }}}`.replace(':product', productId),
        //             type: 'GET',
        //             dataType: 'json',
        //             success: function (data) {
        //                 const qualifier = data.qualifier;
        //                 var uniqueLocationId = "location_id_" + productId;

        //                 const inputHtml = `
        //                     <div class="row justify-center">
        //                         <div class="col-md-4">
        //                             <label>Nama Barang</label>
        //                             <input type="hidden" name="selected_products[${productId}][product_id]" value="${productId}">
        //                             <input type="text" class="form-control mb-3" value="${productName}" disabled>
        //                         </div>
        //                         <div class="col-md-1"></div>
        //                         <div class="col-md-3">
        //                             <label>Lokasi</label>
        //                             <select name="selected_products[${productId}][location][]" id="${uniqueLocationId}" class="form-control mb-3" multiple required></select>
        //                         </div>
        //                         <div class="col-md-1"></div>
        //                         <div class="col-md-2">
        //                             <label>Satuan</label>
        //                             <input type="text" name="selected_products[${productId}][qualifier_id]" class="form-control mb-3" value="${qualifier.name}" placeholder="Qualifier" required>
        //                         </div>
        //                     </div>
        //                     <div id="locations_${productId}"></div>
        //                 `;

        //                 selectedProductsDiv.append(inputHtml);
        //                 selectInput($("#" + uniqueLocationId), locations_url, location_ph);

        //                 const selectedLocations = $(`#${uniqueLocationId}`).select2("data");
        //                 console.log(selectedLocations);
        //                 $(`#locations_${productId}`).empty();
        //                 selectedLocations.forEach(location => {
        //                     const locationId = location.id;
        //                     const locationDetails = getLocationDetails(locationId);
                            
        //                     const inputFields = `
        //                         <div class="row">
        //                             <div class="col-md-4"></div>
        //                             <div class="col-md-4">
        //                                 <label>Nama Lokasi</label>
        //                                 <input type="text" class="form-control mb-3" value="${location.text}" disabled>
        //                             </div>
        //                             <div class="col-md-2">
        //                                 <label>Kadaluarsa</label>
        //                                 <input type="date" name="selected_products[${productId}][location_ids][${location.id}][expired]" class="form-control mb-3" value="${locationDetails.expired}" required>
        //                             </div>
        //                             <div class="col-md-2">
        //                                 <label>Jumlah</label>
        //                                 <input type="number" name="selected_products[${productId}][location_ids][${location.id}][amount]" class="form-control mb-3" value="${locationDetails.amount}" placeholder="Amount" required>
        //                             </div>
        //                         </div>
        //                     `;

        //                     $(`#locations_${productId}`).append(inputFields);
        //                 });

        //                 $(`#${uniqueLocationId}`).on('change', function() {
        //                     const selectedLocations = $(this).select2("data");
        //                     $(`#locations_${productId}`).empty();
        //                     console.log(selectedLocations);

        //                     selectedLocations.forEach(location => {
        //                         const locationId = location.id;
        //                         const locationDetails = getLocationDetails(locationId);
                                
        //                         const inputFields = `
        //                             <div class="row">
        //                                 <div class="col-md-4"></div>
        //                                 <div class="col-md-4">
        //                                     <label>Nama Lokasi</label>
        //                                     <input type="text" class="form-control mb-3" value="${location.text}" disabled>
        //                                 </div>
        //                                 <div class="col-md-2">
        //                                     <label>Kadaluarsa</label>
        //                                     <input type="date" name="selected_products[${productId}][location_ids][${location.id}][expired]" class="form-control mb-3" value="${locationDetails.expired}" required>
        //                                 </div>
        //                                 <div class="col-md-2">
        //                                     <label>Jumlah</label>
        //                                     <input type="number" name="selected_products[${productId}][location_ids][${location.id}][amount]" class="form-control mb-3" value="${locationDetails.amount}" placeholder="Amount" required>
        //                                 </div>
        //                             </div>
        //                         `;

        //                         $(`#locations_${productId}`).append(inputFields);
        //                     });
        //                 });
        //             },
        //             error: function (error) {
        //                 console.error("Error fetching qualifier data:", error);
        //             }
        //         });
        //     });
        // }

        // productsSelect.select2({
        //     width: "100%",
        //     multiple: true,
        //     placeholder: "Tambah Barang",
        // }).on("change", function () {
        //     updateSelectedProducts();
        // });

        // updateSelectedProducts();
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
