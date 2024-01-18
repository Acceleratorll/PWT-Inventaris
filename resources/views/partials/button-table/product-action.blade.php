@can('update product')
<a href="{{ route('product.edit', ['product' => $id]) }}"class="btn btn-success">
    Ubah
</a>
@endcan
<button class="masuk btn btn-info text-white" id="masuk" data-original-title="Masuk" data-id="{{ $id }}" data-transactions="{{ json_encode($product_transactions) }}" data-outgoing-products="{{ json_encode($outgoing_products) }}">Riwayat</button>
@can('delete product')
<button id="delete" data-id="{{ $id }}" data-name="{{ $name }}" data-original-title="Delete" class="delete btn btn-danger">
Hapus
</button>
@endcan
<form action="{{ route('product.destroy',['product' => $id]) }}" id="deleteForm" method="post">
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
                        <a class="nav-link active" id="productTransactions-tab" data-toggle="tab" href="#productTransactions" role="tab" aria-controls="productTransactions" aria-selected="true">Barang Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="outgoingProducts-tab" data-toggle="tab" href="#outgoingProducts" role="tab" aria-controls="outgoingProducts" aria-selected="false">Barang Keluar</a>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <!-- Tab content for Product Transactions -->
                    <div class="tab-pane fade show active" id="productTransactions" role="tabpanel" aria-labelledby="productTransactions-tab">
                        @if (isset($product_transactions) && auth()->user()->can('view transaction'))
                        <div class="table-responsive">
                            <table class="table table-bordered text-center w-100" id="table-transactions">
                                <caption>Table Masuk</caption>
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Saldo Awal</th>
                                        <th scope="col" class="text-center">Masuk</th>
                                        {{-- <th scope="col" class="text-center">Satuan</th> --}}
                                        <th scope="col" class="text-center">Supplier</th>
                                        <th scope="col" class="text-center">Tanggal</th>
                                    </tr>
                                    {{-- @foreach ($product_transactions as $transaction)
                                    <tr>
                                        @if($transaction['transaction']['status'] === 0)
                                        <td scope="col" class="text-center">Waiting</td>
                                        @elseif($transaction['transaction']['status'] === 1)
                                        <td scope="col" class="text-center">Finish</td>
                                        @endif
                                        <td scope="col" class="text-center">{{ $transaction['product_amount'] }}</td>
                                        <td scope="col" class="text-center">{{ $transaction['amount'] }}</td>
                                        <td scope="col" class="text-center">{{ $qualifier['name'] }}</td>
                                        <td scope="col" class="text-center">{{ $transaction['transaction']['supplier']['name'] }}</td>
                                        <td scope="col" class="text-center">{{ Carbon\Carbon::parse($transaction['transaction']['purchase_date'])->format('d-m-Y') }}</td>
                                    </tr>
                                    @endforeach --}}
                                </thead>
                            </table>
                        </div>
                        @else
                        <p>Sorry Yee</p>
                        @endif
                    </div>

                    <!-- Tab content for Outgoing Products -->
                    <div class="tab-pane fade" id="outgoingProducts" role="tabpanel" aria-labelledby="outgoingProducts-tab">
                        @if (isset($outgoing_products) && auth()->user()->can('view rpp'))
                        <div class="table-responsive">
                            <table class="table table-bordered text-center w-100" id="table-process-plans">
                                <caption>Table Keluar</caption>
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="text-center">Saldo Awal</th>
                                        <th scope="col" class="text-center">Keluar</th>
                                        {{-- <th scope="col" class="text-center">Satuan</th> --}}
                                        <th scope="col" class="text-center">Pelanggan</th>
                                        <th scope="col" class="text-center">Tanggal</th>
                                    </tr>
                                    {{-- @foreach ($outgoing_products as $outgoingProduct )
                                        <tr>
                                            <td scope="col" class="text-center">{{ $outgoingproduct['product_amount'] }}</td>
                                            <td scope="col" class="text-center">{{ $outgoingproduct['amount'] }}</td>
                                            <td scope="col" class="text-center">{{ $qualifier['name'] }}</td>
                                            <td scope="col" class="text-center">{{ $outgoingproduct['process_plan']['customer']['name'] }}</td>
                                            <td scope="col" class="text-center">{{ Carbon\Carbon::parse($outgoingproduct['process_plan']['updated_at'])->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach --}}
                                </thead>
                            </table>
                        </div>
                        @else
                        Sorry Yee
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tutup btn btn-outline-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.delete').on('click', function () {
            var deleteButton = $(this);
            var defaultId = deleteButton.data('id');
    
            Swal.fire({
                title: 'Delete Product',
                text: 'Are you sure you want to delete this product?',
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
                        url: `{{ route("product.destroy", ["product" => ":productId"]) }}`.replace(':productId', defaultId),
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'product Deleted Successfully',
                                type: 'success',
                                icon: 'success',
                                timer: 1700,
                                onOpen: function() {
                                    Swal.showLoading()
                                }
                            });
                            location.reload();
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to delete product', 'error');
                        },
                    });
                }
            });
        });

        $('.masuk').on('click', function () {
            var transactionsData = $(this).data('transactions');
            var outgoingProductData = $(this).data('outgoing-products');
            displayProductLocations(transactionsData, outgoingProductData);
        });
    });

    function displayProductLocations(locationsData, outData) {
        $('#transactionHistoryModal').modal('show');

        // Assuming you have a table inside the modal with the ID 'table-locations-modal'
        var table = $('#table-transactions').DataTable({
            data: locationsData,
            columns: [
                { 
                    data: 'transaction.status',
                    title: 'Status',
                    render: function (status) {
                        if (status === 0) {
                            return '<td scope="col" class="text-center">Waiting</td>';
                        } else if (status === 1) {
                            return '<td scope="col" class="text-center">Finished</td>';
                        }
                    }
                },
                { data: 'product_amount', title: 'Saldo Awal' },
                { data: 'amount', title: 'Masuk' },
                // { data: 'qualifier.name', title: 'Satuan' },
                { data: 'transaction.supplier.name', title: 'Supplier' },
                { 
                    data: 'transaction.purchase_date',
                    title: 'Tanggal',
                    render: function (data) {
                        return moment(data).format('DD-MM-YYYY');
                    }
                },
            ],
            destroy: true,
        });

        if(outData.length > 0){
            var table = $('#table-process-plans').DataTable({
                data: outData,
                columns: [
                    { data: 'outgoing_product.status', title: 'Status' },
                    { data: 'product_amount', title: 'Saldo Awal' },
                    { data: 'amount', title: 'Keluar' },
                    // { data: 'qualifier.name', title: 'Satuan' },
                    { data: 'process_plan.customer.name', title: 'Pelanggan' },
                    { 
                        data: 'outgoing_product.updated_at',
                        title: 'Tanggal',
                        render: function (data) {
                            return moment(data).format('DD-MM-YYYY');
                        }
                    },
                ],
                destroy: true, // Destroy the existing table if it exists
            });
        }else{
            var table = $('#table-process-plans').DataTable();
        }
    }

    function closeModal() {
        $('#transactionHistoryModal').modal('hide');
    }
    </script>