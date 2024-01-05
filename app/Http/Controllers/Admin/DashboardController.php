<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcessPlan;
use App\Models\Product;
use App\Repositories\CategoryProductRepository;
use App\Repositories\ProductTransactionRepository;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    protected $categoryRepository;
    protected $productTransactionRepository;

    public function __construct(
        CategoryProductRepository $categoryRepository,
        ProductTransactionRepository $productTransactionRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productTransactionRepository = $productTransactionRepository;
    }

    public function index(): View
    {
        $categories = $this->categoryRepository->all();
        $unusedMax = $categories->max('min');
        $unused = $categories->where('min', $unusedMax)->first();
        $total = Product::all()->count();
        return view('dashboard.index', [
            'categories' => $categories,
            'total' => $total,
            'unused' => $unused,
        ]);
    }

    public function getReportProcessPlans()
    {
        $rpps = ProcessPlan::with('outgoing_products')->get();
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $data = [];

        foreach ($rpps as $rpp) {
            $customer = $rpp->customer;
            $month = Carbon::parse($rpp->created_at)->format('F');

            if (!isset($data[$customer])) {
                $data[$customer] = array_fill_keys($months, 0);
            }

            $data[$customer][$month]++;
        }

        return DataTables::of($data)->make(true);
    }

    public function getUnusedProducts()
    {
        $categories = $this->categoryRepository->all();
        $unusedMax = $categories->max('min');
        $unused = $categories->where('min', $unusedMax)->first();

        $products = Product::where('category_product_id', $unused->id)->get();

        return DataTables::of($products)
            ->addColumn('id', function ($product) {
                return $product->id;
            })
            ->addColumn('name', function ($product) {
                return $product->name;
            })
            ->addColumn('last_used', function ($product) {
                return $product->updated_at->format('d-m-Y');
            })
            ->make(true);
    }
}
