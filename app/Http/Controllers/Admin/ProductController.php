<?php

namespace App\Http\Controllers\Admin;

use App\Events\DataAddedEvent;
use App\Events\UpdateChartEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\CategoryProductRepository;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $productRepository;
    protected $categoryProductRepository;

    public function __construct(ProductRepository $productRepository, CategoryProductRepository $categoryProductRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryProductRepository = $categoryProductRepository;
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
                return $product->material ? $product->material->name : 'N/A';
            })
            ->addColumn('product_type_name', function ($product) {
                return $product->product_type ? $product->product_type->name : 'N/A';
            })
            ->addColumn('qualifier_name', function ($product) {
                return $product->qualifier ? $product->qualifier->name : 'N/A';
            })
            ->addColumn('category_product_name', function ($product) {
                return $product->category_product ? $product->category_product->name : 'N/A';
            })
            ->addColumn('note', function ($product) {
                return $product->note ? $product->note : 'N/A';
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
        $product = $this->productRepository->create($input);

        $category = $this->categoryProductRepository->find($product->category_product_id);
        $count = $category->products->count();

        $data = [
            'id' => $product->category_product_id,
            'name' => $product->category_product->name,
            'qty' => $count,
            'context' => 'create',
        ];

        event(new DataAddedEvent($data, 'Product'));
        event(new UpdateChartEvent('cChart', $data));
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

    public function getJsonProducts(Request $request): JsonResponse
    {
        $products = $this->productRepository->search($request->term);
        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => $product->name
            ];
        });
        return response()->json($formattedProducts);
    }

    public function getJsonProduct(string $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        return response()->json($product);
    }
}
