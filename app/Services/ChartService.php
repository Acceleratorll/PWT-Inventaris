<?php

namespace App\Services;

use App\Repositories\ProductTransactionRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChartService
{
    protected $productTransactionRepository;
    protected $materialRepository;
    protected $productRepository;

    public function __construct(
        ProductTransactionRepository $productTransactionRepository,
        MaterialRepository $materialRepository,
        ProductRepository $productRepository
    ) {
        $this->productTransactionRepository = $productTransactionRepository;
        $this->materialRepository = $materialRepository;
        $this->productRepository = $productRepository;
    }

    public function productTransactionMonthly(): JsonResponse
    {
        $currentMonth = now()->month;

        $productTransactions = $this->productTransactionRepository->getByMonth($currentMonth);

        return $this->productTransactionChartData($productTransactions);
    }

    protected function productTransactionChartData($productTransactions)
    {
        $datasets = [];
        $labels = [];

        $materials = $this->materialRepository->all();

        foreach ($materials as $material) {
            $data = [];
            $labels = [];

            foreach ($productTransactions as $productTransaction) {
                $totalSalesQty = $productTransaction->product_transactions
                    ->where('product.material.id', $material->id)
                    ->sum('qty');

                $data[] = $totalSalesQty;
                $labels[] = $productTransaction->supplier->name;
            }

            $datasets[] = [
                'label' => $material->name,
                'data' => $data,
                'fill' => false,
            ];
        }
        return response()->json([
            'datasets' => $datasets,
            'labels' => $labels
        ]);
    }
}
