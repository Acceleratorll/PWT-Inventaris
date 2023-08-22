<?php

namespace App\Http\Controllers;

use App\Charts\kategoriChart as ChartsKategoriChart;
use App\Charts\stockTintaChart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard');
    }

    public function home(stockTintaChart $chart, ChartsKategoriChart $kategoriChart): View
    {
        $heads = [
            'ID',
            'Warna',
            ['label' => 'Stock', 'width' => 40],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $Kheads = [
            'ID',
            'Barang',
            ['label' => 'Lokasi', 'width' => 40],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
        <i class="fa fa-lg fa-fw fa-pen"></i>
        </button>';
        $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
        <i class="fa fa-lg fa-fw fa-trash"></i>
        </button>';
        $btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
        <i class="fa fa-lg fa-fw fa-eye"></i>
        </button>';

        $config = [
            'data' => [
                [22, 'Cyan', '40g', '<nobr>' . $btnEdit . $btnDelete . $btnDetails . '</nobr>'],
                [19, 'Black', '70g', '<nobr>' . $btnEdit . $btnDelete . $btnDetails . '</nobr>'],
                [3, 'Yellow', '10g', '<nobr>' . $btnEdit . $btnDelete . $btnDetails . '</nobr>'],
            ],
            'order' => [[1, 'asc']],
            'columns' => [null, null, null, ['orderable' => false]],
        ];

        $Kconfig = [
            'data' => [
                [22, 'Mobil', 'Parkiran', '<nobr>' . $btnEdit . $btnDelete . $btnDetails . '</nobr>'],
                [19, 'Motor', 'Bengkel', '<nobr>' . $btnEdit . $btnDelete . $btnDetails . '</nobr>'],
                [3, 'HONDA', 'Rumah', '<nobr>' . $btnEdit . $btnDelete . $btnDetails . '</nobr>'],
            ],
            'order' => [[1, 'asc']],
            'columns' => [null, null, null, ['orderable' => false]],
        ];
        return view('dashboard.index', [
            'chart' => $chart->build(),
            'kategoriChart' => $kategoriChart->build(),
            'heads' => $heads,
            'config' => $config,
            'kheads' => $Kheads,
            'kconfig' => $Kconfig
        ]);
    }
}
