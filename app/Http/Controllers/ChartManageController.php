<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\NotaDinas;
use App\Models\ProcessPlan;
use App\Models\Product;
use App\Models\Transaction;
use App\Repositories\CategoryProductRepository;
use App\Repositories\MaterialRepository;
use App\Services\ChartService;
use App\Services\ChartServiceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChartManageController extends Controller
{
    protected $categoryRepository;
    protected $materialRepository;
    protected $chartService;

    public function __construct(
        CategoryProductRepository $categoryRepository,
        MaterialRepository $materialRepository,
        ChartService $chartService,
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->materialRepository = $materialRepository;
        $this->chartService = $chartService;
    }

    public function transactionMonthly(): JsonResponse
    {
        return $this->chartService->transactionMonthly();
    }

    public function planPaperFirst(): JsonResponse
    {
        $currentYear = now()->year;
        $datas = Transaction::whereYear('created_at', $currentYear)->get();
        $plans = NotaDinas::whereYear('to_date', $currentYear)->get();

        $datasets = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June'];

        $data = [];
        $dataPlan = [];
        foreach ($months as $index => $month) {
            $realData = $datas->filter(function ($item) use ($index) {
                return $item->purchase_date->month == $index + 1 &&
                    $item->product_transactions->contains('product.material.name', 'Bahan Kertas');
            })->sum(function ($item) {
                return $item->product_transactions->where('product.material.name', 'Bahan Kertas')->sum('amount');
            });

            $planData = $plans->filter(function ($item) use ($index) {
                $fromDate = $item->from_date instanceof Carbon ? $item->from_date : Carbon::parse($item->from_date);
                return $fromDate->month == $index + 1 &&
                    $item->product_plannings->contains('product.material.name', 'Bahan Kertas');
            })->sum(function ($item) {
                return $item->product_plannings->where('product.material.name', 'Bahan Kertas')->sum('procurement_plan_amount');
            });

            if ($data) {
                $data[] = $data[$index - 1] + $realData;
                $dataPlan[] = $dataPlan[$index - 1] + $planData;
            } else {
                $data[] = $realData;
                $dataPlan[] = $planData;
            }
        }



        $datasets = [
            'realData' => $data,
            'planData' => $dataPlan,
            'fill' => false,
        ];

        return response()->json(['datasets' => $datasets, 'labels' => $months]);
    }

    public function planPaperSecond(): JsonResponse
    {
        $currentYear = now()->year;
        $datas = Transaction::whereYear('created_at', $currentYear)->get();
        $plans = NotaDinas::whereYear('to_date', $currentYear)->get();
        $types = Material::all();

        $datasets = [];
        $months = ['July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $data = [];
        $dataPlan = [];
        foreach ($months as $index => $month) {
            $realData = $datas->filter(function ($item) use ($index) {
                return $item->purchase_date->month == $index + 7 &&
                    $item->product_transactions->contains('product.material.name', 'Bahan Kertas');
            })->sum(function ($item) {
                return $item->product_transactions->where('product.material.name', 'Bahan Kertas')->sum('amount');
            });

            $planData = $plans->filter(function ($item) use ($index) {
                $fromDate = $item->from_date instanceof Carbon ? $item->from_date : Carbon::parse($item->from_date);
                return $fromDate->month == $index + 7 &&
                    $item->product_plannings->contains('product.material.name', 'Bahan Kertas');
            })->sum(function ($item) {
                return $item->product_plannings->where('product.material.name', 'Bahan Kertas')->sum('procurement_plan_amount');
            });

            if ($data) {
                $data[] = $data[$index - 1] + $realData;
                $dataPlan[] = $dataPlan[$index - 1] + $planData;
            } else {
                $data[] = $realData;
                $dataPlan[] = $planData;
            }
        }



        $datasets = [
            'realData' => $data,
            'planData' => $dataPlan,
            'fill' => false,
        ];

        return response()->json(['datasets' => $datasets, 'labels' => $months]);
    }

    public function planInkFirst(): JsonResponse
    {
        $currentYear = now()->year;
        $datas = Transaction::whereYear('created_at', $currentYear)->get();
        $plans = NotaDinas::whereYear('to_date', $currentYear)->get();

        $datasets = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June'];

        $data = [];
        $dataPlan = [];
        foreach ($months as $index => $month) {
            $realData = $datas->filter(function ($item) use ($index) {
                return $item->purchase_date->month == $index + 1 &&
                    $item->product_transactions->contains('product.material.name', 'Bahan Tinta');
            })->sum(function ($item) {
                return $item->product_transactions->where('product.material.name', 'Bahan Tinta')->sum('amount');
            });

            $planData = $plans->filter(function ($item) use ($index) {
                $fromDate = $item->from_date instanceof Carbon ? $item->from_date : Carbon::parse($item->from_date);
                return $fromDate->month == $index + 1 &&
                    $item->product_plannings->contains('product.material.name', 'Bahan Tinta');
            })->sum(function ($item) {
                return $item->product_plannings->where('product.material.name', 'Bahan Tinta')->sum('procurement_plan_amount');
            });

            if ($data) {
                $data[] = $data[$index - 1] + $realData;
                $dataPlan[] = $dataPlan[$index - 1] + $planData;
            } else {
                $data[] = $realData;
                $dataPlan[] = $planData;
            }
        }



        $datasets = [
            'realData' => $data,
            'planData' => $dataPlan,
            'fill' => false,
        ];

        return response()->json(['datasets' => $datasets, 'labels' => $months]);
    }

    public function planInkSecond(): JsonResponse
    {
        $currentYear = now()->year;
        $datas = Transaction::whereYear('created_at', $currentYear)->get();
        $plans = NotaDinas::whereYear('to_date', $currentYear)->get();

        $datasets = [];
        $months = ['July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $data = [];
        $dataPlan = [];
        foreach ($months as $index => $month) {
            $realData = $datas->filter(function ($item) use ($index) {
                return $item->purchase_date->month == $index + 7 &&
                    $item->product_transactions->contains('product.material.name', 'Bahan Tinta');
            })->sum(function ($item) {
                return $item->product_transactions->where('product.material.name', 'Bahan Tinta')->sum('amount');
            });

            $planData = $plans->filter(function ($item) use ($index) {
                $fromDate = $item->from_date instanceof Carbon ? $item->from_date : Carbon::parse($item->from_date);
                return $fromDate->month == $index + 7 &&
                    $item->product_plannings->contains('product.material.name', 'Bahan Tinta');
            })->sum(function ($item) {
                return $item->product_plannings->where('product.material.name', 'Bahan Tinta')->sum('procurement_plan_amount');
            });

            if ($data) {
                $data[] = $data[$index - 1] + $realData;
                $dataPlan[] = $dataPlan[$index - 1] + $planData;
            } else {
                $data[] = $realData;
                $dataPlan[] = $planData;
            }
        }



        $datasets = [
            'realData' => $data,
            'planData' => $dataPlan,
            'fill' => false,
        ];

        return response()->json(['datasets' => $datasets, 'labels' => $months]);
    }

    public function tintaMonthly(): JsonResponse
    {
        $currentMonth = now()->month;
        $materials = $this->materialRepository->all();
        $datasets = [];

        foreach ($materials as $material) {
            $processPlans = ProcessPlan::with(['outgoing_products.product.material'])
                ->whereMonth('created_at', $currentMonth)
                ->whereHas('outgoing_products.product.material')
                ->get();

            $data = [];
            $labels = [];

            foreach ($processPlans as $processPlan) {
                $totalSalesQty = $processPlan->outgoing_products
                    ->where('product.material_id', $material->id)
                    ->sum('amount');
                $data[] = $totalSalesQty;
                $labels[] = $processPlan->customer->name;
            }

            $datasets[] = [
                'label' => $material->name,
                'data' => $data,
                'fill' => false,
            ];
        }

        return response()->json(['datasets' => $datasets, 'labels' => $labels]);
    }

    public function rppYearly(): JsonResponse
    {
        $currentYear = now()->year;
        $datas = ProcessPlan::whereYear('created_at', $currentYear)->get();
        $types = Material::all();

        $datasets = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($types as $type) {
            $data = [];
            foreach ($months as $index => $month) {
                $totalQty = $datas->filter(function ($item) use ($index, $type) {
                    return $item->outed_date->month == $index + 1 &&
                        $item->outgoing_products->contains('product.material_id', $type->id);
                })->sum(function ($item) use ($type) {
                    return $item->outgoing_products->where('product.material_id', $type->id)->sum('amount');
                });

                $data[] = $totalQty;
            }

            $datasets[] = [
                'label' => $type->name,
                'data' => $data,
                'fill' => false,
            ];
        }

        return response()->json(['datasets' => $datasets, 'labels' => $months]);
    }

    public function categoryOverall(): JsonResponse
    {
        $categories = $this->categoryRepository->all();

        foreach ($categories as $category) {
            $labels[] = $category->name;
            $datas[] = $category->products->count();
        };


        return response()->json(['datas' => $datas, 'labels' => $labels]);
    }
}
