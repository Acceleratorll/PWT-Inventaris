<?php

namespace App\Services;

use App\Repositories\OutgoingProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\MaterialRepository;
use App\Events\UpdateChartEvent;

class ProcessPlanService
{
    protected $outgoingProductRepository;
    protected $productRepository;
    protected $materialRepository;

    public function __construct(
        OutgoingProductRepository $outgoingProductRepository,
        ProductRepository $productRepository,
        MaterialRepository $materialRepository
    ) {
        $this->outgoingProductRepository = $outgoingProductRepository;
        $this->productRepository = $productRepository;
        $this->materialRepository = $materialRepository;
    }

    public function updateOutgoingProducts($rpp, $selectedProducts)
    {
        $amountChanges = [];

        foreach ($rpp->outgoing_products as $outgoingProduct) {
            $productId = $outgoingProduct->product_id;

            if (!isset($selectedProducts[$productId])) {
                $this->deleteOutgoingProduct($outgoingProduct, $amountChanges);
            } else {
                $this->updateOutgoingProduct($outgoingProduct, $selectedProducts[$productId], $amountChanges);
            }
        }

        foreach ($selectedProducts as $productId => $productData) {
            if (!$rpp->outgoing_products->contains('product_id', $productId)) {
                $this->createOutgoingProduct($rpp, $productId, $productData, $amountChanges);
            }
        }

        return $amountChanges;
    }

    private function deleteOutgoingProduct($outgoingProduct, &$amountChanges)
    {
        $amountChanges[$outgoingProduct->product_id] = -$outgoingProduct->qty;
        $outgoingProduct->delete();
    }

    private function updateOutgoingProduct($outgoingProduct, $productData, &$amountChanges)
    {
        $netChange = $productData['qty'] - $outgoingProduct->qty;
        $amountChanges[$outgoingProduct->product_id] = $netChange;

        $outgoingProduct->qty = $productData['qty'];
        $outgoingProduct->save();
    }

    private function createOutgoingProduct($rpp, $productId, $productData, &$amountChanges)
    {
        $inputOutPro = [
            'process_plan_id' => $rpp->id,
            'product_id' => $productId,
            'qty' => $productData['qty'],
        ];

        $this->outgoingProductRepository->create($inputOutPro);

        $amountChanges[$productId] = $productData['qty'];
    }

    public function updateProductAmounts($amountChanges)
    {
        foreach ($amountChanges as $productId => $netChange) {
            $product = $this->productRepository->find($productId);
            $product->amount -= $netChange;
            $product->save();
        }
    }

    public function updateChart($rpp)
    {
        $materials = $this->materialRepository->all();

        $data = [];
        $labels = [];

        foreach ($materials as $material) {
            $totalSalesQty = $rpp->outgoing_products
                ->where('product.material.id', $material->id)
                ->sum('qty');
            $data[] = $totalSalesQty;
            $labels[] = $material->name;
        }

        $addedData = [
            'name' => $rpp->customer->name,
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
