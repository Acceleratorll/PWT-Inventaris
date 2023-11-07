<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\ProcessPlan;
use App\Models\Product;
use App\Repositories\CategoryProductRepository;
use App\Repositories\MaterialRepository;
use App\Services\ChartService;
use App\Services\ChartServiceService;
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

    public function productTransactionMonthly(): JsonResponse
    {
        return $this->chartService->productTransactionMonthly();
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
                    ->where('product.material.id', $material->id)
                    ->sum('qty');
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
        $types = Material::all(); // Assuming ProductType is a model for your product types.

        $datasets = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($types as $type) {
            $data = [];
            foreach ($months as $index => $month) {
                $totalQty = $datas->filter(function ($item) use ($index, $type) {
                    return $item->created_at->month == $index + 1 &&
                        $item->outgoing_products->contains('product.material_id', $type->id);
                })->sum(function ($item) use ($type) {
                    return $item->outgoing_products->where('product.material_id', $type->id)->sum('qty');
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


    // public function rppYearly(): JsonResponse
    // {
    //     $currentYear = now()->year;
    //     $datas = ProcessPlan::whereYear('created_at', $currentYear)->get();
    //     $totalSales = [];
    //     $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    //     foreach ($months as $index => $month) {
    //         $totalSales[] = $datas->filter(function ($item) use ($index) {
    //             return $item->created_at->month == $index + 1;
    //         })->count();
    //     }

    //     return response()->json(['datas' => $totalSales, 'labels' => $months]);
    // }

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
