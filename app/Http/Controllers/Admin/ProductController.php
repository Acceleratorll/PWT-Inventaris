<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index(): View
    {
        return view('product.index');
    }

    public function getProducts()
    {
        return DataTables::of($this->productRepository->all())
            ->addColumn('action', 'partials.button-table.product-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function destroy(string $id)
    {
        $this->productRepository->delete($id);
        return redirect()->back()->with('success', 'Product berhasil dihapus');
    }
}
