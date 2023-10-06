<?php

namespace App\Http\Controllers\Admin;

use App\Events\AddChartEvent;
use App\Events\DataAddedEvent;
use App\Events\DeleteChartEvent;
use App\Events\DeletedDataEvent;
use App\Events\UpdateChartEvent;
use App\Events\UpdateDataEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryProductRequest;
use App\Models\CategoryProduct;
use App\Repositories\CategoryProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryProductController extends Controller
{
    protected $categoryProductRepository;

    public function __construct(CategoryProductRepository $categoryProductRepository)
    {
        $this->categoryProductRepository = $categoryProductRepository;
    }


    public function index(): View
    {
        return view('category.index');
    }

    public function getCategories()
    {
        $categories = $this->categoryProductRepository->all();

        return DataTables::of($categories)
            ->addColumn('name', function ($category) {
                return $category->name;
            })
            ->addColumn('max', function ($category) {
                return $category->max;
            })
            ->addColumn('created_at', function ($category) {
                return $category->created_at->format('d-m-Y');
            })
            ->addColumn('updated_at', function ($category) {
                return $category->updated_at->format('d-m-Y');
            })
            ->addColumn('action', 'partials.button-table.category-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create(): View
    {
        return view('category.create');
    }

    public function store(CategoryProductRequest $request)
    {
        $input = $request->validated();
        $category = $this->categoryProductRepository->create($input);
        $data = $category->products->count();
        $count = 0;

        if ($category->products) {
            $count = $category->products->count();
        }

        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'qty' => $count,
            'context' => 'create',
        ];

        event(new AddChartEvent('cChart', $data));
        event(new DataAddedEvent($data, 'Category'));
        return redirect()->route('category.index')->with('success', 'Kategori berhasil dibuat !');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id): View
    {
        $category = $this->categoryProductRepository->find($id);
        return view('category.edit', compact('category'));
    }

    public function update(CategoryProductRequest $request, string $id)
    {
        $input = $request->validated();
        $category = $this->categoryProductRepository->find($id);

        $categoryUpdated = $this->categoryProductRepository->update($id, $input);

        $data = [
            'id' => $id,
            'name' => $category->name,
            'newName' => $categoryUpdated->name,
            'qty' => $categoryUpdated->products->count(),
            'context' => 'update',
        ];

        event(new UpdateChartEvent('cChart', $data));
        event(new UpdateDataEvent($data, 'Kategori'));
        return redirect()->route('category.index')->with('success', 'Kategori berhasil diubah !');
    }

    public function destroy(string $id)
    {
        $category = $this->categoryProductRepository->find($id);

        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'qty' => $category->products->count(),
            'context' => 'delete',
        ];

        $this->categoryProductRepository->delete($id);
        event(new DeletedDataEvent($data, 'Category'));
        event(new DeleteChartEvent('cChart', $data));
        return redirect()->back()->with('success', 'Category have been Removed');
    }

    public function getJsonCategories(Request $request): JsonResponse
    {
        $categories = $this->categoryProductRepository->search($request->term);
        $formattedCategories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'text' => $category->name,
            ];
        });
        return response()->json($formattedCategories);
    }

    public function getJsonCategory($category): JsonResponse
    {
        $category = $this->categoryProductRepository->find($category);
        return response()->json($category);
    }
}
