<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTransactionRequest;
use App\Repositories\IncomingProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductTransactionRepository;
use App\Services\ProductTransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductTransactionController extends Controller
{
    protected $productTransactionRepository;
    protected $productRepository;
    protected $incomingProductRepository;
    protected $productTransactionService;

    public function __construct(
        ProductTransactionRepository $productTransactionRepository,
        ProductRepository $productRepository,
        IncomingProductRepository $incomingProductRepository,
        ProductTransactionService $productTransactionService,
    ) {
        $this->productTransactionRepository = $productTransactionRepository;
        $this->productRepository = $productRepository;
        $this->incomingProductRepository = $incomingProductRepository;
        $this->productTransactionService = $productTransactionService;
    }

    public function index(): View
    {
        return view('product_transaction.index');
    }

    public function getTransactions()
    {
        $productTransactions = $this->productTransactionRepository->all();

        return DataTables::of($productTransactions)
            ->addColumn('id', function ($productTransaction) {
                return $productTransaction->id;
            })
            ->addColumn('code', function ($productTransaction) {
                return $productTransaction->code;
            })
            ->addColumn('supplier', function ($productTransaction) {
                return $productTransaction->supplier->name;
            })
            ->addColumn('formatted_purchase_date', function ($productTransaction) {
                return $productTransaction->purchase_date->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_created_at', function ($productTransaction) {
                return $productTransaction->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($productTransaction) {
                return $productTransaction->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.product-transaction-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        return view('product_transaction.create');
    }

    public function store(ProductTransactionRequest $productTransactionRequest)
    {
        $input = $productTransactionRequest->validated();
        $transaction = $this->productTransactionRepository->create($input);

        $amountChanges = $this->productTransactionService->updateIncomingProducts($transaction, $input['selected_products']);
        $this->productTransactionService->updateProductAmounts($amountChanges);
        return redirect()->back()->with('success', 'Product Transaction created successfully !');
    }

    public function show(string $id)
    {
        $productTransaction = $this->productTransactionRepository->find($id);
        return response()->json($productTransaction);
    }

    public function edit(string $id): View
    {
        $productTransaction = $this->productTransactionRepository->find($id);
        return view('product_transaction.edit', compact('productTransaction'));
    }

    public function update(ProductTransactionRequest $request, string $id)
    {
        $input = $request->validated();
        $this->productTransactionRepository->update($id, $input);
        $transaction = $this->productTransactionRepository->find($id);

        $amountChanges = $this->productTransactionService->updateIncomingProducts($transaction, $input['selected_products']);
        $this->productTransactionService->updateProductAmounts($amountChanges);

        return redirect()->route('productTransaction.index')->with('success', 'PRODUCTTRANSACTION berhasil diupdate!');
    }

    private function updateOutgoingProducts($productTransaction, $selectedProducts, &$amountChanges)
    {
        foreach ($selectedProducts as $productId => $productData) {
            $outgoingProduct = $productTransaction->outgoing_products->firstWhere('product_id', $productId);

            if ($outgoingProduct) {
                $netChange = $productData['qty'] - $outgoingProduct->qty;
                if ($netChange <= 0) {
                    $this->updateAmountChanges($amountChanges, $productId, $netChange);
                }

                $outgoingProduct->qty = $productData['qty'];
                $outgoingProduct->save();
            } else {
                $inputOutPro = [
                    'process_plan_id' => $productTransaction->id,
                    'product_id' => $productId,
                    'qty' => $productData['qty'],
                ];

                $this->outgoingProductRepository->create($inputOutPro);

                $netChange = $productData['qty'];
                $this->updateAmountChanges($amountChanges, $productId, $netChange);
            }
        }
    }

    private function updateProductAmounts($amountChanges)
    {
        foreach ($amountChanges as $productId => $netChange) {
            $product = $this->productRepository->find($productId);
            $product->amount += $netChange;
            $product->save();
        }
    }

    private function updateChart($productTransaction)
    {
        $materials = $this->materialRepository->all();

        $data = [];
        $labels = [];

        foreach ($materials as $material) {
            $totalSalesQty = $productTransaction->outgoing_products
                ->where('product.material.id', $material->id)
                ->sum('qty');
            $data[] = $totalSalesQty;
            $labels[] = $material->name;
        }

        $addedData = [
            'name' => $productTransaction->customer,
            'qty' => $data,
            'context' => 'update'
        ];

        // event(new UpdateChartEvent('tChart', $addedData));
    }


    private function updateAmountChanges(&$amountChanges, $productId, $netChange)
    {
        if (!isset($amountChanges[$productId])) {
            $amountChanges[$productId] = 0;
        }
        $amountChanges[$productId] += $netChange;
    }

    public function destroy(string $id)
    {
        $productTransaction = $this->productTransactionRepository->find($id);
        $qty = $this->productTransactionRepository->qtyCurrentMonth(now()->month, now()->year);
        $data = [
            'name' => $productTransaction->customer,
            'qty' => $qty,
            'context' => 'delete',
        ];

        $datasets = [
            'name' => now()->format('M'),
            'qty' => (int)$qty - 1,
            'context' => 'delete',
        ];

        $this->productTransactionRepository->delete($id);

        // event(new DeleteChartEvent('tChart', $data));
        // event(new UpdateChartEvent('rChart', $datasets));
        // event(new DeletedDataEvent($data, 'productTransaction'));

        return redirect()->back()->with('success', 'productTransaction berhasil dihapus');
    }

    public function exportproductTransactions()
    {
        $productTransactions = $this->productTransactionRepository->all();

        return (new productTransactionsExport($productTransactions))->download('process_plans.xlsx');
    }

    public function importproductTransactions()
    {
        try {
            DB::beginTransaction();

            Excel::import(new productTransactionsImport, request()->file('file'));
            DB::commit();

            return redirect()->back()->with('success', 'Import successful');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
