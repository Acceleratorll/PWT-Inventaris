<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class stockTintaChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build()
    {
        return $this->chart->pieChart()
            // ->setTitle('Stock Keseluruhan Tinta')
            // ->setSubtitle('Sekarang')
            ->addData([10, 100, 3])
            ->setDataLabels(1)
            ->setLabels(['Banyak = 10', 'Normal = 100', 'Tipis = 3']);
    }
}
