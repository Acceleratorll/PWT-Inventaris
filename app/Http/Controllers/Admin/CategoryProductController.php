<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryProductRequest;
use App\Models\CategoryProduct;
use App\Repositories\CategoryProductRepository;
use Illuminate\Contracts\View\View;
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
        return DataTables::of(CategoryProduct::query())
            ->addColumn('action', 'partials.button-table.category-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $category = $this->categoryProductRepository->find($id);
        return view('category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryProductRequest $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->categoryProductRepository->delete($id);
        return back()->with('message', 'Category have been Removed');
    }
}
