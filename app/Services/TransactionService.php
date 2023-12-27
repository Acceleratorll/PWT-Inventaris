<?php

namespace App\Services;

use App\Events\AddChartEvent;
use App\Events\DataAddedEvent;
use App\Events\ProductNotificationEvent;
use App\Events\UpdateChartEvent;
use App\Events\UpdateDataEvent;
use App\Exports\TransactionExport;
use App\Imports\TransactionImport;
use App\Notifications\CriticalProduct;
use App\Notifications\WarningProduct;
use App\Repositories\IncomingProductRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductLocationRepository;
use App\Repositories\ProductTransactionRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransactionService
{
    protected $productRepository;
    protected $productTransactionRepository;
    protected $productLocationRepository;
    protected $materialRepository;
    protected $transactionRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductTransactionRepository $productTransactionRepository,
        ProductLocationRepository $productLocationRepository,
        MaterialRepository $materialRepository,
        TransactionRepository $transactionRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->productTransactionRepository = $productTransactionRepository;
        $this->productLocationRepository = $productLocationRepository;
        $this->materialRepository = $materialRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function all()
    {
        return $this->transactionRepository->all();
    }

    public function storeIncomingProducts($transaction, $selectedProducts)
    {
        foreach ($selectedProducts as $productId => $productData) {
            if ($transaction->product_transactions == null || !$transaction->product_transactions->contains('product_id', $productId)) {
                // Product is not in the existing product_transactions, create it
                $this->createIncomingProduct($transaction, $productId, $productData, $amountChanges);
            }
        }
    }

    private function deleteIncomingProduct($incomingProduct, &$amountChanges)
    {
        $amountChanges[$incomingProduct->product_id] = -$incomingProduct->amount;
        $incomingProduct->delete();
    }

    private function updateIncomingProduct($incomingProduct, $productData, &$amountChanges)
    {
        $product = $incomingProduct->product;
        foreach ($productData['location_ids'] as $locationId => $locationData) {
            $inputOutLoc = [
                'product_id' => $product->id,
                'location_id' => $locationId,
                'amount' => $locationData['amount'],
                'expired' => $locationData['expired'],
            ];

            $incomeLoc = $this->productLocationRepository->find()->update($inputOutLoc);
            $product->product_locations()->save($incomeLoc);
            $amount += $locationData['amount'];
        }
        $netChange = $productData['qty'] - $incomingProduct->amount;
        $amountChanges[$incomingProduct->product_id] = $netChange;

        $incomingProduct->amount = $productData['qty'];
        $incomingProduct->save();
    }

    private function createIncomingProduct($transaction, $productId, $productData, &$amountChanges)
    {
        $product = $this->productRepository->find($productId);
        $oriAmount = $product->total_amount;
        (int) $amount = 0;

        foreach ($productData['location_ids'] as $locationId => $locationData) {
            $proLoc = $this->productLocationRepository->findByProductExpiredLocation($product->id, $locationId, $locationData['expired']);

            if (!$proLoc) {
                $inputOutLoc = [
                    'product_id' => $product->id,
                    'location_id' => $locationId,
                    'amount' => $locationData['amount'],
                    'purchase_date' => $transaction->purchase_date,
                    'expired' => $locationData['expired'],
                ];

                $incomeLoc = $this->productLocationRepository->create($inputOutLoc);
                $product->product_locations()->save($incomeLoc);
            } else {
                $proLoc->update(['amount' => $proLoc->amount += $locationData['amount']]);
            }
            $amount += $locationData['amount'];
        }

        $inputOutPro = [
            'transaction_id' => $transaction->id,
            'product_id' => $productId,
            'amount' => (int)$amount,
            'product_amount' => $oriAmount,
        ];

        $income = $this->productTransactionRepository->create($inputOutPro);
        $transaction->product_transactions()->save($income);
    }

    public function updateProductAmounts($amountChanges)
    {
        foreach ($amountChanges as $productId => $netChange) {
            $product = $this->productRepository->find($productId);
            $product->total_amount += $netChange;
            $product->save();

            if ($product->total_amount <= $product->minimal_amount) {
                $product->update(['category_product_id']);
            }
        }
    }

    public function addChart($transaction)
    {
        $materials = $this->materialRepository->all();
        $transaction_find = $this->transactionRepository->find($transaction->id);
        $data = [];
        $labels = [];
        foreach ($materials as $material) {
            $totalSalesQty = $material->products()->sum('total_amount');
            $data[] = $totalSalesQty;
            $labels[] = $material->name;
        }

        $datasets[] = [
            'labels' => $labels,
            'qty' => $data,
        ];

        $chartData = [
            'name' => $transaction_find->supplier->name,
            'qty' => $data,
            'context' => 'create'
        ];
        event(new AddChartEvent('pChart', $chartData));
        event(new DataAddedEvent($chartData, 'Transaction'));
    }

    public function updateChart($transaction)
    {
        $materials = $this->materialRepository->all();
        $transaction_find = $this->transactionRepository->find($transaction->id);
        $data = [];
        $labels = [];
        foreach ($materials as $material) {
            $totalSalesQty = $transaction_find->product_transactions
                ->where('product.material_id', $material->id)
                ->sum('qty');
            $data[] = $totalSalesQty;
            $labels[] = $material->name;
        }

        $datasets[] = [
            'labels' => $labels,
            'qty' => $data,
        ];

        $chartData = [
            'name' => $transaction_find->supplier->name,
            'qty' => $data,
            'context' => 'update'
        ];
        event(new UpdateChartEvent('pChart', $chartData));
        event(new UpdateDataEvent($chartData, 'Transaction'));
        $this->notifyProduct($transaction_find);
    }

    public function getTransactionBySupplierName($label)
    {
        return $this->transactionRepository->getBySupplierName($label);
    }

    public function notifyProduct($transaction)
    {
        $user = auth()->user();
        foreach ($transaction->product_transactions as $iProduct) {
            $product = $this->productRepository->find($iProduct->product_id);
            if ($product->amount < (0.1 * $product->max_amount)) {
                auth()->user()->notify(new CriticalProduct($product));
                event(new ProductNotificationEvent('critical', $product));
            } else if ($product->amount < (0.3 * $product->max_amount)) {
                auth()->user()->notify(new WarningProduct($product));
                event(new ProductNotificationEvent('warning', $product));
            }
        }
    }

    private function updateAmountChanges(&$amountChanges, $productId, $netChange)
    {
        if (!isset($amountChanges[$productId])) {
            $amountChanges[$productId] = 0;
        }
        $amountChanges[$productId] += $netChange;
    }

    public function exportAll()
    {
        $transaction = $this->transactionRepository->all();

        return (new TransactionExport($transaction))->download('product_transaction.xlsx');
    }

    public function import()
    {
        try {
            DB::beginTransaction();

            Excel::import(new TransactionImport, request()->file('file'));
            DB::commit();

            return redirect()->back()->with('success', 'Import successful');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Import failed: Supplier or Product values arent exist in database. Please input first and try again');
        }
    }
}
