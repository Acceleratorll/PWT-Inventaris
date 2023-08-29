<?php

namespace App\Charts;

use App\Models\ProcessPlan;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class monthlyUsedTintaChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\AreaChart
    {
        $currentMonth = now()->month;

        $processPlans = ProcessPlan::with(['outgoing_products.product.material'])
            ->whereMonth('created_at', $currentMonth)
            ->whereHas('outgoing_products.product.material', function ($query) {
                $query->where('id', 3);
            })
            ->get();

        $data = [];
        $labels = [];

        foreach ($processPlans as $processPlan) {
            $totalSalesQty = $processPlan->outgoing_products->sum('qty');

            $data[] = $totalSalesQty;
            $labels[] = $processPlan->customer;
        }

        return $this->chart->areaChart()
            ->setTitle('Penggunaan Tinta setiap RPP per Bulan')
            ->addData('Total Tinta', $data)
            ->setXAxis($labels)
            ->setColors(['#FF5733'])
            ->setGrid();
    }
}
