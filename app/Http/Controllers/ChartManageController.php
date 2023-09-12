<?php

namespace App\Http\Controllers;

use App\Models\ProcessPlan;
use App\Models\Product;
use App\Repositories\CategoryProductRepository;
use App\Repositories\MaterialRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChartManageController extends Controller
{

    protected $categoryRepository;
    protected $materialRepository;

    public function __construct(CategoryProductRepository $categoryRepository, MaterialRepository $materialRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->materialRepository = $materialRepository;
    }

    public function tintaMonthly(): JsonResponse
    {
        $currentMonth = now()->month;
        $materials = $this->materialRepository->all();
        $datasets = [];

        foreach ($materials as $material) {
            $processPlans = ProcessPlan::with(['outgoing_products.product.material'])
                ->whereMonth('created_at', $currentMonth)
                ->whereHas('outgoing_products.product.material', function ($query) use ($material) {
                    $query->where('id', $material->id);
                })
                ->get();

            $data = [];
            $labels = [];

            foreach ($processPlans as $processPlan) {
                $totalSalesQty = $processPlan->outgoing_products
                    ->where('product.material.id', $material->id)
                    ->sum('qty'); // Sum quantity for this specific material
                $data[] = $totalSalesQty;
                $labels[] = $processPlan->customer;
            }

            // Create a dataset for the current material
            $datasets[] = [
                'label' => $material->name, // Material name as label
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
        $totalSales = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($months as $index => $month) {
            $totalSales[] = $datas->filter(function ($item) use ($index) {
                return $item->created_at->month == $index + 1;
            })->count();
        }

        return response()->json(['datas' => $totalSales, 'labels' => $months]);
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