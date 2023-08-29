<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
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
        $products = $this->productRepository->all();
        return DataTables::of($products)
            ->addColumn('material_name', function ($product) {
                return $product->material->name;
            })
            ->addColumn('product_type_name', function ($product) {
                return $product->product_type->name;
            })
            ->addColumn('qualifier_name', function ($product) {
                return $product->qualifier->name;
            })
            ->addColumn('category_product_name', function ($product) {
                return $product->category_product->name;
            })
            ->addColumn('note', function ($product) {
                return $product->note;
            })
            ->addColumn('created_at', function ($product) {
                return $product->created_at->format('d-m-Y');
            })
            ->addColumn('updated_at', function ($product) {
                return $product->updated_at->format('d-m-Y');
            })
            ->addColumn('action', 'partials.button-table.product-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create(): View
    {
        return view('product.create');
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $input = $request->validated();
        $this->productRepository->create($input);
        return redirect()->route('product.index')->with('success', 'Product created successfully');
    }

    public function edit($id): View
    {
        $product = $this->productRepository->find($id);
        return view('product.edit', compact('product'));
    }

    public function update($id, ProductRequest $request): RedirectResponse
    {
        $input = $request->validated();
        $this->productRepository->update($id, $input);
        return redirect()->route('product.index')->with('success', 'Product updated successfully');
    }

    public function destroy(string $id)
    {
        $this->productRepository->delete($id);
        return redirect()->back()->with('success', 'Product berhasil dihapus');
    }

    public function getJsonProducts(): HttpFoundationResponse
    {
        $products = $this->productRepository->all();
        return response()->json($products);
    }
}
