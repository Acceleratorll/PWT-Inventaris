<?php

namespace App\Http\Controllers\Admin;

use App\Events\UpdateChartEvent;
use App\Exports\TransactionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Imports\TransactionImport;
use App\Repositories\LocationRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use App\Services\TransactionService;
use Carbon\Carbon;
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
    protected $locationRepository;
    protected $transactionService;

    public function __construct(
        TransactionRepository $transactionRepository,
        ProductRepository $productRepository,
        LocationRepository $locationRepository,
        TransactionService $transactionService,
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->productRepository = $productRepository;
        $this->locationRepository = $locationRepository;
        $this->transactionService = $transactionService;
    }

    public function index(): View
    {
        return view('transaction.index');
    }

    public function finish(): View
    {
        return view('transaction.finish');
    }

    public function wait(): View
    {
        return view('transaction.wait');
    }

    public function allTransactions()
    {
        $data = $this->transactionService->all();
        return response()->json($data);
    }

    public function getTransactions()
    {
        $transactions = $this->transactionRepository->all();
        return $this->transactionService->table($transactions);
    }

    public function getWaitTransactions()
    {
        $transactions = $this->transactionRepository->getByStatus(0);
        return $this->transactionService->table($transactions);
    }

    public function getFinishTransactions()
    {
        $transactions = $this->transactionRepository->getByStatus(1);
        return $this->transactionService->table($transactions);
    }

    public function create(): View
    {
        return view('transaction.create');
    }

    public function store(TransactionRequest $transactionRequest): RedirectResponse
    {
        $input = $transactionRequest->validated();
        $transaction = $this->transactionRepository->create($input);
        $this->transactionService->storeIncomingProducts($transaction, $input['selected_products']);
        // $this->transactionService->updateProductAmounts($amountChanges);
        $this->transactionService->addChart($transaction);
        return redirect()->route('transaction.index')->with('success', 'Product Transaction created successfully !');
    }

    public function show(string $id): JsonResponse
    {
        $transaction = $this->transactionRepository->find($id);
        return response()->json($transaction);
    }

    public function isiPesanan($id): View
    {
        $transaction = $this->transactionRepository->find($id);
        return view('transaction.editPesanan', compact('transaction'));
    }

    public function edit(string $id): View
    {
        $transaction = $this->transactionRepository->find($id);
        $locations = $this->locationRepository->all();

        return view('transaction.edit', compact('transaction', 'locations'));
    }

    public function update(TransactionRequest $request, string $id)
    {
        $input = $request->validated();
        dd($input);
        // $amount_changes = $request->;
        $transaction = $this->transactionRepository->find($id);
        $this->transactionRepository->update($id, $input);


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
