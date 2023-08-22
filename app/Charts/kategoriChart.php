<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class kategoriChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build()
    {
        return $this->chart->donutChart()
            // ->setTitle('Kategori Barang')
            // ->setSubtitle('Season 2021.')
            ->addData([240, 30, 10])
            ->setDataLabels(1)
            ->setLabels(['Daily = 240', 'Slow = 30', 'Unused = 10']);
    }
}
