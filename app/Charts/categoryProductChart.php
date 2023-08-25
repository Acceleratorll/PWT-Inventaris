<?php

namespace App\Charts;

use App\Models\Product;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class categoryProductChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\DonutChart
    {
        $daily = Product::where('category_product_id', 1)->get()->count();
        $slow = Product::where('category_product_id', 2)->get()->count();
        $unused = Product::where('category_product_id', 3)->get()->count();

        return $this->chart->donutChart()
            ->setTitle('Presentase Barang')
            ->addData([$daily, $slow, $unused])
            ->setDataLabels(1)
            ->setLabels(['Sering Dipakai', 'Slow Moving', 'Tidak Pernah dipakai'])
            ->setColors(['#52b788', '#ffd60a', '#d00000'])
            ->setFontColor('gray');
    }
}
