<?php

namespace App\Services;

use App\Events\UpdateChartEvent;
use App\Repositories\MaterialRepository;
use App\Repositories\OutgoingProductRepository;
use App\Repositories\ProcessPlanRepository;
use App\Repositories\ProductRepository;
use App\Repositories\QualifierRepository;

class ProcessPlanService
{
    private $productRepository;
    private $outgoingProductRepository;
    private $qualifierRepository;
    private $materialRepository;

    public function setControllerDependencies(
        ProductRepository $productRepository,
        OutgoingProductRepository $outgoingProductRepository,
        QualifierRepository $qualifierRepository,
        MaterialRepository $materialRepository
    ) {
        $this->productRepository = $productRepository;
        $this->outgoingProductRepository = $outgoingProductRepository;
        $this->qualifierRepository = $qualifierRepository;
        $this->materialRepository = $materialRepository;
    }

    public function updateOutgoingProducts($processPlan, $selectedProducts)
    {
        foreach ($selectedProducts as $productId => $productData) {
            // $product = $this->productRepository->find($productId);
            $outgoingProduct = $processPlan->outgoing_products->firstWhere('product_id', $productId);

            $conversionFactor = $this->qualifierRepository->find($productData['qualifier_id'])->conversion_factor ?? 1;
            $convertedQuantity = $productData['qty'] * $conversionFactor;

            if ($outgoingProduct) {
                $outgoingProduct->update([
                    'qty' => $convertedQuantity,
                    'qualifier_id' => $productData['qualifier_id'],
                ]);
                dd('updated', $outgoingProduct);
            } else {
                $outgoing = $this->outgoingProductRepository->create([
                    'process_plan_id' => $processPlan->id,
                    'product_id' => $productId,
                    'qty' => $convertedQuantity,
                    'qualifier_id' => $productData['qualifier_id'],
                ]);

                dd('created', $outgoing);

                $netChange = $productData['qty'];
                if (!isset($amountChanges[$productId])) {
                    $amountChanges[$productId] = 0;
                }
                $amountChanges[$productId] += $netChange;
            }
        }
    }

    public function updateProductAmounts($selectedProducts)
    {
        $amountChanges = [];

        foreach ($selectedProducts as $productId => $productData) {
            $netChange = $productData['qty'];
            if (!isset($amountChanges[$productId])) {
                $amountChanges[$productId] = 0;
            }
            $amountChanges[$productId] += $netChange;
        }

        foreach ($amountChanges as $productId => $netChange) {
            $product = $this->productRepository->find($productId);
            $product->amount += $netChange;
            $product->save();
        }
    }

    public function updateChart($processPlan)
    {
        $materials = $this->materialRepository->all();

        $data = [];
        $labels = [];

        foreach ($materials as $material) {
            $totalSalesQty = $processPlan->outgoing_products
                ->where('product.material.id', $material->id)
                ->sum('qty');
            $data[] = $totalSalesQty;
            $labels[] = $material->name;
        }

        $addedData = [
            'name' => $processPlan->customer,
            'qty' => $data,
            'context' => 'update'
        ];

        event(new UpdateChartEvent('tChart', $addedData));
    }
}
