<?php

namespace App\Http\Controllers\Admin;

use App\Events\UpdateChartEvent;
use App\Exports\TransactionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Imports\TransactionImport;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use App\Services\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    protected $transactionRepository;
    protected $productRepository;
    protected $transactionService;

    public function __construct(
        TransactionRepository $transactionRepository,
        ProductRepository $productRepository,
        TransactionService $transactionService,
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->productRepository = $productRepository;
        $this->transactionService = $transactionService;
    }

    public function index(): View
    {
        return view('transaction.index');
    }

    public function getTransactions()
    {
        $transactions = $this->transactionRepository->all();

        return DataTables::of($transactions)
            ->addColumn('id', function ($transaction) {
                return $transaction->id;
            })
            ->addColumn('code', function ($transaction) {
                return $transaction->code;
            })
            ->addColumn('supplier', function ($transaction) {
                return $transaction->supplier->name;
            })
            ->addColumn('formatted_purchase_date', function ($transaction) {
                return $transaction->purchase_date->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_created_at', function ($transaction) {
                return $transaction->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($transaction) {
                return $transaction->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.product-transaction-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create(): View
    {
        return view('transaction.create');
    }

    public function store(TransactionRequest $transactionRequest): RedirectResponse
    {
        $input = $transactionRequest->validated();
        $transaction = $this->transactionRepository->create($input);
        $amountChanges = $this->transactionService->updateIncomingProducts($transaction, $input['selected_products']);
        $this->transactionService->updateProductAmounts($amountChanges);
        $this->transactionService->addChart($transaction);
        return redirect()->back()->with('success', 'Product Transaction created successfully !');
    }

    public function show(string $id): JsonResponse
    {
        $transaction = $this->transactionRepository->find($id);
        return response()->json($transaction);
    }

    public function edit(string $id): View
    {
        $transaction = $this->transactionRepository->find($id);
        return view('transaction.edit', compact('transaction'));
    }

    public function update(TransactionRequest $request, string $id)
    {
        $input = $request->validated();
        $this->transactionRepository->update($id, $input);
        $transaction = $this->transactionRepository->find($id);

        $amountChanges = $this->transactionService->updateIncomingProducts($transaction, $input['selected_products']);
        $this->transactionService->updateProductAmounts($amountChanges);
        $this->transactionService->updateChart($transaction);

        return redirect()->route('transaction.index')->with('success', 'TRANSACTION berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        $transaction = $this->transactionRepository->find($id);
        $qty = $this->transactionRepository->qtyCurrentMonth(now()->month, now()->year);
        $data = [
            'name' => $transaction->customer,
            'qty' => $qty,
            'context' => 'delete',
        ];

        $datasets = [
            'name' => now()->format('M'),
            'qty' => (int)$qty - 1,
            'context' => 'delete',
        ];

        $this->transactionRepository->delete($id);

        event(new UpdateChartEvent('pChart', $datasets));

        return redirect()->back()->with('success', 'transaction berhasil dihapus');
    }

    public function exportTransactions()
    {
        return $this->transactionService->exportAll();
    }

    public function importTransactions()
    {
        return $this->transactionService->import();
    }

    public function getJsonTransactionBySupplierName($supplierName)
    {
        $transaction = $this->transactionRepository->getBySupplierName((string)$supplierName);

        return response()->json($transaction);
    }
}
