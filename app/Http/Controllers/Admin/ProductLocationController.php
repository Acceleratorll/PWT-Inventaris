<?php

namespace App\Http\Controllers\Admin;

use App\Events\UpdateChartEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductLocationRequest;
use App\Repositories\ProductLocationRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use App\Services\ProductLocationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductLocationController extends Controller
{
    protected $productLocationRepository;
    protected $productLocationService;
    protected $productRepository;
    protected $transactionRepository;

    public function __construct(
        ProductLocationRepository $productLocationRepository,
        ProductLocationService $productLocationService,
        ProductRepository $productRepository,
        TransactionRepository $transactionRepository,
    ) {
        $this->productLocationRepository = $productLocationRepository;
        $this->productLocationService = $productLocationService;
        $this->productRepository = $productRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function index(): View
    {
        return view('productLocation.index');
    }

    public function create(): View
    {
        return view('productLocation.create');
    }

    public function show($id): JsonResponse
    {
        $data = $this->productLocationRepository->find($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit(): View
    {
        return view();
    }

    public function update($id, ProductLocationRequest $productLocationRequest)
    {
        try {
            DB::transaction(function () use ($id, $productLocationRequest) {
                $input = $productLocationRequest->validated();
            });
        } catch (\Throwable $th) {
        }
    }

    public function store(ProductLocationRequest $productLocationRequest)
    {
        $input = $productLocationRequest->validated();
        $error = false;
        $msg = 'An error occurred during the transaction.';
        try {
            DB::transaction(function () use ($input, &$error, &$msg) {
                $transaction = $this->transactionRepository->find($input['transaction_id']);
                foreach ($input['selected_products'] as $productId => $productData) {
                    $realAmount = $transaction->product_transactions->where('product_id', $productId)->first()->amount;
                    $locationAmounts = array_column($productData['location_ids'], 'amount');
                    $sumLocationAmounts = array_sum($locationAmounts);

                    if ($realAmount != $sumLocationAmounts) {
                        $error = true;
                        $msg = 'Invalid amount. Amount not matched';
                        return;
                    }

                    foreach ($productData['location_ids'] as $locationId => $locationData) {
                        $proLoc = $this->productLocationRepository->findByProductExpiredLocation($productId, $locationId, $locationData['expired']);
                        if ($locationData['expired'] <= now()) {
                            $error = true;
                            $msg = 'Invalid expired date';
                            return;
                        }

                        if (!$proLoc) {
                            $inputOutLoc = [
                                'product_id' => $productId,
                                'location_id' => $locationId,
                                'amount' => $locationData['amount'],
                                'purchase_date' => $input['purchase_date'],
                                'expired' => $locationData['expired'],
                            ];

                            $this->productLocationRepository->create($inputOutLoc);
                        } else {
                            $proLoc->update(['amount' => $proLoc->amount += $locationData['amount']]);
                        }
                    }
                }

                $transaction->update(['status' => 1]);
            });

            $data = [
                'name' => 'Transaksi',
            ];
            event(new UpdateChartEvent('pPChart', $data));
        } catch (\Throwable $th) {
            $error = true;
            $msg = $th->getMessage();
        }

        if ($error) {
            return redirect()->back()->with('error', $msg);
        }

        return redirect()->route('transaction.index')->with('success', 'Product Location Added Successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->productLocationRepository->delete($id);

        $data = [
            'name' => 'Transaksi',
        ];

        event(new UpdateChartEvent('pPChart', $data));
        return response()->json(['message' => 'Data has been found!'], 200);
    }

    public function table()
    {
        $data = $this->productLocationRepository->all();
        return $this->productLocationService->table($data);
    }

    public function select(Request $request)
    {
        $term = $request->input('term');
        return $this->productLocationService->select($term);
    }

    public function selectWithParam(Request $request)
    {
        $term = $request->input('term');
        $productId = $request->input('data');
        return $this->productLocationService->selectWithParam($term, $productId);
    }
}
