<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChartService
{
    protected $transactionRepository;
    protected $materialRepository;
    protected $productRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        MaterialRepository $materialRepository,
        ProductRepository $productRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->materialRepository = $materialRepository;
        $this->productRepository = $productRepository;
    }

    public function transactionMonthly(): JsonResponse
    {
        $currentMonth = now()->month;

        $transactions = $this->transactionRepository->getByMonth($currentMonth);

        return $this->transactionChartData($transactions);
    }

    protected function transactionChartData($transactions)
    {
        $datasets = [];
        $labels = [];

        $materials = $this->materialRepository->all();

        foreach ($materials as $material) {
            $data = [];
            $labels = [];

            foreach ($transactions as $transaction) {
                $totalSalesQty = $transaction->product_transactions
                    ->where('product.material.id', $material->id)
                    ->sum('amount');

                $data[] = $totalSalesQty;
                $labels[] = $transaction->supplier->name;
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
