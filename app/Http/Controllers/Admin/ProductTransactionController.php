<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductTransactionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTransactionRequest;
use App\Imports\ProductTransactionImport;
use App\Repositories\IncomingProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductTransactionRepository;
use App\Services\ProductTransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

    public function create(): View
    {
        return view('product_transaction.create');
    }

    public function store(ProductTransactionRequest $productTransactionRequest): RedirectResponse
    {
        $input = $productTransactionRequest->validated();
        $transaction = $this->productTransactionRepository->create($input);
        $amountChanges = $this->productTransactionService->updateIncomingProducts($transaction, $input['selected_products']);
        $this->productTransactionService->updateProductAmounts($amountChanges);
        $this->productTransactionService->addChart($transaction);
        return redirect()->back()->with('success', 'Product Transaction created successfully !');
    }

    public function show(string $id): JsonResponse
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
        $this->productTransactionService->updateChart($transaction);

        return redirect()->route('productTransaction.index')->with('success', 'PRODUCTTRANSACTION berhasil diupdate!');
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

    public function exportProductTransactions()
    {
        return $this->productTransactionService->exportAll();
    }

    public function importProductTransactions()
    {
        return $this->productTransactionService->import();
    }

    public function getJsonProductTransactionBySupplierName($supplierName)
    {
        $productTransaction = $this->productTransactionRepository->getBySupplierName((string)$supplierName);

        return response()->json($productTransaction);
    }
}
