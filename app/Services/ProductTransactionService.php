<?php

namespace App\Services;

use App\Events\UpdateChartEvent;
use App\Repositories\IncomingProductRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\ProductRepository;

class ProductTransactionService
{
    protected $incomingProductRepository;
    protected $productRepository;
    protected $materialRepository;

    public function __construct(
        IncomingProductRepository $incomingProductRepository,
        ProductRepository $productRepository,
        MaterialRepository $materialRepository
    ) {
        $this->incomingProductRepository = $incomingProductRepository;
        $this->productRepository = $productRepository;
        $this->materialRepository = $materialRepository;
    }

    public function updateIncomingProducts($transaction, $selectedProducts)
    {
        $amountChanges = [];

        // Loop through existing incoming_products
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

        // Loop through selected products
        foreach ($selectedProducts as $productId => $productData) {
            if (!$transaction->incoming_products->contains('product_id', $productId)) {
                // Product is not in the existing incoming_products, create it
                $this->createIncomingProduct($transaction, $productId, $productData, $amountChanges);
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

        $this->incomingProductRepository->create($inputOutPro);

        $amountChanges[$productId] = $productData['qty'];
    }

    public function updateProductAmounts($amountChanges)
    {
        foreach ($amountChanges as $productId => $netChange) {
            $product = $this->productRepository->find($productId);
            $product->amount += $netChange;
            $product->save();
        }
    }

    public function updateChart($transaction)
    {
        $materials = $this->materialRepository->all();

        $data = [];
        $labels = [];

        foreach ($materials as $material) {
            $totalSalesQty = $transaction->incoming_products
                ->where('product.material.id', $material->id)
                ->sum('qty');
            $data[] = $totalSalesQty;
            $labels[] = $material->name;
        }

        $addedData = [
            'name' => $transaction->supplier,
            'qty' => $data,
            'context' => 'update'
        ];

        event(new UpdateChartEvent('tChart', $addedData));
    }

    private function updateAmountChanges(&$amountChanges, $productId, $netChange)
    {
        if (!isset($amountChanges[$productId])) {
            $amountChanges[$productId] = 0;
        }
        $amountChanges[$productId] += $netChange;
    }
}
