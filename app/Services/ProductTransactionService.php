<?php

namespace App\Services;

use App\Events\AddChartEvent;
use App\Events\DataAddedEvent;
use App\Events\ProductNotificationEvent;
use App\Events\UpdateChartEvent;
use App\Events\UpdateDataEvent;
use App\Exports\ProductTransactionExport;
use App\Imports\ProductTransactionImport;
use App\Notifications\CriticalProduct;
use App\Notifications\WarningProduct;
use App\Repositories\IncomingProductRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductTransactionRepository;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductTransactionService
{
    protected $incomingProductRepository;
    protected $productRepository;
    protected $materialRepository;
    protected $productTransactionRepository;

    public function __construct(
        IncomingProductRepository $incomingProductRepository,
        ProductRepository $productRepository,
        MaterialRepository $materialRepository,
        ProductTransactionRepository $productTransactionRepository,
    ) {
        $this->incomingProductRepository = $incomingProductRepository;
        $this->productRepository = $productRepository;
        $this->materialRepository = $materialRepository;
        $this->productTransactionRepository = $productTransactionRepository;
    }

    public function updateIncomingProducts($transaction, $selectedProducts)
    {
        $amountChanges = [];

        foreach ($selectedProducts as $productId => $productData) {
            if (!$transaction->incoming_products->contains('product_id', $productId)) {
                // Product is not in the existing incoming_products, create it
                $this->createIncomingProduct($transaction, $productId, $productData, $amountChanges);
            }
        }

        foreach ($transaction->incoming_products as $incomingProduct) {
            $productId = $incomingProduct->product_id;

            // Check if the product is not present in the selectedProducts
            if (!isset($selectedProducts[$productId])) {
                // Delete the incoming_product
                $this->deleteIncomingProduct($incomingProduct, $amountChanges);
            } else {
                // Update the existing incoming_product
                $this->updateIncomingProduct($incomingProduct, $selectedProducts[$productId], $amountChanges);
            }
        }

        return $amountChanges;
    }

    private function deleteIncomingProduct($incomingProduct, &$amountChanges)
    {
        $amountChanges[$incomingProduct->product_id] = -$incomingProduct->qty;
        $incomingProduct->delete();
    }

    private function updateIncomingProduct($incomingProduct, $productData, &$amountChanges)
    {
        $netChange = $productData['qty'] - $incomingProduct->qty;
        $amountChanges[$incomingProduct->product_id] = $netChange;

        $incomingProduct->qty = $productData['qty'];
        $incomingProduct->save();
    }

    private function createIncomingProduct($transaction, $productId, $productData, &$amountChanges)
    {
        $inputOutPro = [
            'product_transaction_id' => $transaction->id,
            'product_id' => $productId,
            'qty' => $productData['qty'],
        ];

        $income = $this->incomingProductRepository->create($inputOutPro);
        $transaction->incoming_products()->save($income);

        $amountChanges[$productId] = $productData['qty'];
    }

    public function updateProductAmounts($amountChanges)
    {
        foreach ($amountChanges as $productId => $netChange) {
            $product = $this->productRepository->find($productId);
            $product->amount += $netChange;
            $product->save();

            if ($product->amount > (0.3 * $product->max_amount)) {
                $product->update(['category_product_id']);
            } else if ($product->amount > (0.1 * $product->max_amount)) {
            }
        }
    }

    public function addChart($transaction)
    {
        $materials = $this->materialRepository->all();
        $transaction_find = $this->productTransactionRepository->find($transaction->id);
        $data = [];
        $labels = [];
        foreach ($materials as $material) {
            $totalSalesQty = $transaction_find->incoming_products
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
            'context' => 'create'
        ];
        event(new AddChartEvent('pChart', $chartData));
        event(new DataAddedEvent($chartData, 'Transaction'));
    }

    public function updateChart($transaction)
    {
        $materials = $this->materialRepository->all();
        $transaction_find = $this->productTransactionRepository->find($transaction->id);
        $data = [];
        $labels = [];
        foreach ($materials as $material) {
            $totalSalesQty = $transaction_find->incoming_products
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

    public function getProductTransactionBySupplierName($label)
    {
        return $this->productTransactionRepository->getBySupplierName($label);
    }

    public function notifyProduct($transaction)
    {
        $user = auth()->user();
        foreach ($transaction->incoming_products as $iProduct) {
            $product = $this->productRepository->find($iProduct->product_id);
            if ($product->amount < (0.1 * $product->max_amount)) {
                $user->notify(new CriticalProduct($product));
                event(new ProductNotificationEvent('critical', $product));
            } else if ($product->amount < (0.3 * $product->max_amount)) {
                $user->notify(new WarningProduct($product));
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
        $productTransaction = $this->productTransactionRepository->all();

        return (new ProductTransactionExport($productTransaction))->download('product_transaction.xlsx');
    }

    public function import()
    {
        try {
            DB::beginTransaction();

            Excel::import(new ProductTransactionImport, request()->file('file'));
            DB::commit();

            return redirect()->back()->with('success', 'Import successful');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Import failed: Supplier or Product values arent exist in database. Please input first and try again');
        }
    }
}
