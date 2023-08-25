<?php

namespace App\Charts;

use App\Models\ProcessPlan;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class yearlyRppChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\AreaChart
    {
        $currentYear = now()->year;
        $datas = ProcessPlan::whereYear('created_at', $currentYear)->get();
        $totalSales = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($months as $index => $month) {
            $totalSales[] = $datas->filter(function ($item) use ($index) {
                return $item->created_at->month == $index + 1;
            })->count();
        }

        return $this->chart->areaChart()
            ->setTitle('Statistik RPP di Tahun '.$currentYear)
            ->setSubtitle('Total RPP di tahun ' . $currentYear . ' adalah ' . $datas->count())
            ->addData('Banyak RPP', array_values($totalSales))
            // ->addData('Digital sales', [70, 29, 77, 28, 55, 45])
            ->setXAxis($months);
    }
}
