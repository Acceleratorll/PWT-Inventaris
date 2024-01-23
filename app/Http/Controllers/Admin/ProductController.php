<?php

namespace App\Http\Controllers\Admin;

use App\Events\DataAddedEvent;
use App\Events\DeletedDataEvent;
use App\Events\ProductNotificationEvent;
use App\Events\UpdateChartEvent;
use App\Events\UpdateDataEvent;
use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Imports\ProductsImport;
use App\Imports\RolesImport;
use App\Notifications\CriticalProduct;
use App\Notifications\WarningProduct;
use App\Repositories\CategoryProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    protected $productRepository;
    protected $userRepository;
    protected $categoryProductRepository;

    public function __construct(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        CategoryProductRepository $categoryProductRepository
    ) {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->categoryProductRepository = $categoryProductRepository;
    }

    public function index(): View
    {
        $category = $this->categoryProductRepository->all();
        return view('product.index', compact('category'));
    }

    public function getProductsByCategory($category)
    {
        $products = $this->productRepository->getByCategory($category);
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
            ->make(true);
    }

    public function getAllProducts()
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
            ->addColumn('outgoing_products', function ($product) {
                return $product->outgoing_products ?? 'N/A';
            })
            ->addColumn('product_transactions', function ($product) {
                return $product->product_transactions ?? 'N/A';
            })
            ->addColumn('history', function ($product) {
                return [$product->product_transactions, $product->outgoing_products];
            })
            ->addColumn('total_amount', function ($product) {
                return $product->total_amount;
            })
            ->addColumn('minimal_amount', function ($product) {
                return $product->minimal_amount;
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
            ->make(true);
    }

    public function getThisYear()
    {
        $products = $this->productRepository->getByThisYear();
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
            ->addColumn('outgoing_products', function ($product) {
                return $product->outgoing_products ?? 'N/A';
            })
            ->addColumn('product_transactions', function ($product) {
                return $product->product_transactions ?? 'N/A';
            })
            ->addColumn('history', function ($product) {
                return [$product->product_transactions, $product->outgoing_products];
            })
            ->addColumn('total_amount', function ($product) {
                return $product->total_amount;
            })
            ->addColumn('minimal_amount', function ($product) {
                return $product->minimal_amount;
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
            ->make(true);
    }

    public function getWarningProducts()
    {
        $products = $this->productRepository->getWarning();
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
            ->make(true);
    }

    public function getDangerProducts()
    {
        $products = $this->productRepository->getDanger();
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

        if ($product->total_amount <= $product->minimal_amount) {
            $users = $this->userRepository->all();
            foreach ($users as $user) {
                $user->notify(new CriticalProduct($product));
                $notif = $user->unreadNotifications->where('data.type', 'critical')->last();
                event(new ProductNotificationEvent('critical', $product, $notif->data['message']));
            }
        }

        $data = [
            'id' => $product->category_product_id,
            'name' => $product->category_product->name,
            'qty' => $count,
            'context' => 'create',
        ];

        event(new DataAddedEvent($data, 'Product'));
        return redirect()->route('product.index')->with('success', 'Product created successfully');
    }

    public function edit($id): View
    {
        $product = $this->productRepository->find($id);
        return view('product.edit', compact('product'));
    }

    public function update($product, ProductRequest $request): RedirectResponse
    {
        $input = $request->validated();
        $product = $this->productRepository->find($product);
        $this->productRepository->update($product->id, $input);
        $new = $this->productRepository->find($product->id);
        $category = $this->categoryProductRepository->find($request->category_product_id);
        $category_old = $product->category_product;

        $data = [
            'id' => $category->id,
            'id_old' => $product->category_product_id,
            'name_old' => $product->category_product->name,
            'name' => $category->name,
            'qty_old' => $category_old->products->count(),
            'qty' => $category->products->count(),
            'context' => 'update',
        ];

        $dataPro = [
            'id' => $new->id,
            'name' => $new->name,
            'updated_at' => $new->updated_at,
            'created_at' => $new->created_at,
        ];

        if ($new->total_amount <= $new->minimal_amount) {
            $users = $this->userRepository->all();
            foreach ($users as $user) {
                $user->notify(new CriticalProduct($new));
                $notif = $user->unreadNotifications->where('data.type', 'critical')->last();
                event(new ProductNotificationEvent('critical', $new, $notif->data['message']));
            }
        }

        event(new UpdateDataEvent($data, 'Product'));

        return redirect()->route('product.index')->with('success', 'Product Updated Successfully');
    }

    public function destroy($product)
    {
        $product = $this->productRepository->find($product);
        $this->productRepository->delete($product->id);
        $category = $this->categoryProductRepository->find($product->category_product_id);

        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'qty' => $category->products->count(),
            'context' => 'delete',
        ];

        event(new DeletedDataEvent($data, 'Product'));
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

    public function getJsonProductsByCategory($category): JsonResponse
    {
        $products = $this->productRepository->getByCategory($category);
        return response()->json($products);
    }

    public function getJsonProduct($product_id): JsonResponse
    {
        $product = $this->productRepository->find($product_id);
        return response()->json($product);
    }

    public function exportProducts()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function importProducts()
    {
        try {
            DB::transaction(function () {
                Excel::import(new ProductsImport, request()->file('file'));
            });

            $data = [
                'name' => 'Import',
                'context' => 'create',
            ];

            event(new DataAddedEvent($data, 'Import'));
            return redirect()->back()->with('success', 'Import successful');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
