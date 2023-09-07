<?php

namespace App\Http\Controllers;

use App\Models\ProcessPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChartManageController extends Controller
{
    public function tintaMonthly(): JsonResponse
    {
        $currentMonth = now()->month;

        $processPlans = ProcessPlan::with(['outgoing_products.product.material'])
            ->whereMonth('created_at', $currentMonth)
            ->whereHas('outgoing_products.product.material', function ($query) {
                $query->where('id', 3);
            })
            ->get();

        foreach ($processPlans as $processPlan) {
            $totalSalesQty = $processPlan->outgoing_products->sum('qty');

            $data[] = $totalSalesQty;
            $labels[] = $processPlan->customer;
        }

        return response()->json(['datas' => $data, 'labels' => $labels]);
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
}
