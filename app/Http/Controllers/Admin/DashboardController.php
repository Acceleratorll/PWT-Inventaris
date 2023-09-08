<?php

namespace App\Http\Controllers\Admin;

use App\Charts\categoryProductChart;
use App\Charts\monthlyUsedTintaChart;
use App\Charts\yearlyRppChart;
use App\Http\Controllers\Controller;
use App\Models\ProcessPlan;
use App\Models\Product;
use App\Repositories\CategoryProductRepository;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryProductRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    // Contoh Area Chart
    public function index(): View
    {
        $categories = $this->categoryRepository->all();
        $unusedMax = $categories->max('max');
        $unused = $categories->where('max', $unusedMax)->first();
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


        // =========>> JS for View
        // $(function() {
        //        $('#results-table').DataTable({
        //        processing: true,
        //        serverSide: true,
        //        ajax: '{{ route('get-report-process-plan') }}',
        //        columns: [
        //            { data: 'customer', name: 'customer' },
        //            { data: 'January', name: 'January' },
        //            { data: 'February', name: 'February' },
        //            { data: 'March', name: 'March' },
        //            { data: 'April', name: 'April' },
        //            { data: 'May', name: 'May' },
        //            { data: 'June', name: 'June' },
        //            { data: 'July', name: 'July' },
        //            { data: 'August', name: 'August' },
        //            { data: 'September', name: 'September' },
        //            { data: 'October', name: 'October' },
        //            { data: 'November', name: 'November' },
        //            { data: 'December', name: 'December' },
        //         ]
        //     });
        //     console.log(data);
        //  });
    }

    public function getUnusedProducts()
    {
        $categories = $this->categoryRepository->all();
        $unusedMax = $categories->max('max');
        $unused = $categories->where('max', $unusedMax)->first();

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
