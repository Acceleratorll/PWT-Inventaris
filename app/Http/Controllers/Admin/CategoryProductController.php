<?php

namespace App\Http\Controllers\Admin;

use App\Events\DataAddedEvent;
use App\Events\DeleteChartEvent;
use App\Events\DeletedDataEvent;
use App\Events\UpdateChartEvent;
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
        $label = $category->name;
        $data = $category->products->count();
        $count = 0;

        if ($category->products) {
            $count = $category->products->count();
        }

        $data = [
            'name' => $category->name,
            'count' => $count,
        ];

        event(new UpdateChartEvent('cChart', $label, $data));
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
        $this->categoryProductRepository->update($id, $input);
        return redirect()->route('category.index')->with('success', 'Kategori berhasil diubah !');
    }

    public function destroy(string $id)
    {
        $category = $this->categoryProductRepository->find($id);
        $data = [
            'id' => $category->id,
            'name' => $category->name,
        ];
        event(new DeleteChartEvent('cChart', $category->name));
        event(new DeletedDataEvent($data, 'Category'));
        $this->categoryProductRepository->delete($id);
        return back()->with('message', 'Category have been Removed');
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
}
