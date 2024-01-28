@can('update location')
<a href="{{ route('location.edit', ['location' => $id]) }}" data-original-title="Edit" class="edit btn btn-success edit">
    Ubah
</a>
@endcan
@can('update product location')
<button
    id="masuk"
    data-id="{{ $id }}"
    data-locations="{{ json_encode($product_locations) }}"
    class="masuk btn btn-primary"
    data-bs-toggle="modal"
    data-bs-target="#transactionHistoryModal"   >
    Detail
</button>
@endcan
@can('delete location')
<button id="delete" data-id="{{ $id }}" data-original-title="Delete" class="delete btn btn-danger">
Hapus
</button>
@endcan
<form action="{{ route('location.destroy',['location' => $id]) }}" id="deleteForm" method="post">
@csrf
@method("DELETE")
</form>
<!-- HTML structure for the modal -->
<div class="modal fade" id="transactionHistoryModal" tabindex="-1" role="dialog" aria-labelledby="transactionHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionHistoryModalLabel">Riwayat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="transactionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="productTransactions-tab" data-toggle="tab" href="#productTransactions" role="tab" aria-controls="productTransactions" aria-selected="true">Barang Kertas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="outgoingProducts-tab" data-toggle="tab" href="#outgoingProducts" role="tab" aria-controls="outgoingProducts" aria-selected="false">Barang Tinta</a>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    <!-- Tab content for Product Transactions -->
                    @if (isset($product_locations) && auth()->user()->can('view product location'))
                    <div class="tab-pane fade show active" id="productTransactions" role="tabpanel" aria-labelledby="productTransactions-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center w-100" id="table-kertas">
                                <caption>Tabel Barang</caption>
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="text-center">Kode</th>
                                        <th scope="col" class="text-center">Nama</th>
                                        <th scope="col" class="text-center">Stock</th>
                                        <th scope="col" class="text-center">Satuan</th>
                                        <th scope="col" class="text-center">Kadaluarsa</th>
                                    </tr>
                                </thead>
                                {{-- @foreach ($product_locations as $location)
                                    @if ($location['product']['material']['name'] === 'Bahan Kertas')
                                        <tr>
                                            <td>{{ $location['product']['product_code'] }}</td>
                                            <td>{{ $location['product']['name'] }}</td>
                                            <td>{{ $location['amount'] }}</td>
                                            <td>{{ $location['product']['qualifier']['name'] }}</td>
                                            <td>{{ Carbon\Carbon::parse($location['expired'])->format('d-m-Y') }}</td>
                                        </tr>
                                    @endif
                                @endforeach --}}
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Tab content for Outgoing Products -->
                    <div class="tab-pane fade" id="outgoingProducts" role="tabpanel" aria-labelledby="outgoingProducts-tab">
                        @if (isset($product_locations))
                            <div class="table-responsive">
                                <table class="table table-bordered text-center w-100" id="table-tinta">
                                    <caption>Tabel Barang</caption>
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col" class="text-center">Kode</th>
                                            <th scope="col" class="text-center">Nama</th>
                                            <th scope="col" class="text-center">Stock</th>
                                            <th scope="col" class="text-center">Satuan</th>
                                            <th scope="col" class="text-center">Kadaluarsa</th>
                                        </tr>
                                    </thead>
                                    {{-- @foreach ($product_locations as $location)
                                        @if ($location['product']['material']['name'] === 'Bahan Tinta')
                                            <tr>
                                                <td>{{ $location['product']['product_code'] }}</td>
                                                <td>{{ $location['product']['name'] }}</td>
                                                <td>{{ $location['amount'] }}</td>
                                                <td>{{ $location['product']['qualifier']['name'] }}</td>
                                                <td>{{ Carbon\Carbon::parse($location['expired'])->format('d-m-Y') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach --}}
                                    <tbody></tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive {
        padding: 0;
    }
</style>

<script>
    $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
            Swal.fire({
                title: 'Delete LOCATION',
                text: 'Are you sure you want to delete this LOCATION?',
                type: 'warning',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                console.log(result);
                if (result.value == true) {
                    console.log('confirmed');
                    $.ajax({
                        type: 'POST',
                        url: `{{ route("location.destroy", ["location" => ":locationId"]) }}`.replace(':locationId', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'LOCATION Deleted Successfully',
                                type: 'success',
                                icon: 'success',
                                timer: 1700,
                            });
                            
    
                            $('#table').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to delete LOCATION', 'error');
                        },
                    });
                }
            });
        });

        $('.masuk').on('click', function () {
            var locationsData = $(this).data('locations');
            displayProductLocations(locationsData);
        });
    });

    function displayProductLocations(locationsData) {
        $('#transactionHistoryModal').modal('show');

        // Assuming you have a table inside the modal with the ID 'table-locations-modal'
        var table = $('#table-kertas').DataTable({
            data: filterProductLocations(locationsData, 1),
            columns: [
                { data: 'product.product_code', title: 'Code' },
                { data: 'product.name', title: 'Nama' },
                { data: 'amount', title: 'Stock' },
                { data: 'product.qualifier.name', title: 'Satuan' },
                { 
                    data: 'expired',
                    title: 'Expired',
                    render: function (data) {
                        return moment(data).format('DD-MM-YYYY');
                    }
                },
            ],
            destroy: true,
        });

        var table = $('#table-tinta').DataTable({
            data: filterProductLocations(locationsData, 2),
            columns: [
                { data: 'product.product_code', title: 'Code' },
                { data: 'product.name', title: 'Nama' },
                { data: 'amount', title: 'Stock' },
                { data: 'product.qualifier.name', title: 'Satuan' },
                { 
                    data: 'expired',
                    title: 'Expired',
                    render: function (data) {
                        return moment(data).format('DD-MM-YYYY');
                    }
                },
            ],
            destroy: true, // Destroy the existing table if it exists
        });
    }

    function filterProductLocations(locationsData, num) {
        return locationsData.filter(function(location) {
            return location.product.material_id === num;
        });
    }

    function closeModal() {
        $('#transactionHistoryModal').modal('hide');
    }
</script>