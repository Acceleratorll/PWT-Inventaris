<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductLocationRequest;
use App\Services\ProductLocationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductLocationController extends Controller
{
    protected $productLocationService;

    public function __construct(
        ProductLocationService $productLocationService,
    ) {
        $this->productLocationService = $productLocationService;
    }

    public function index(): View
    {
        return view('productLocation.index');
    }

    public function create(): View
    {
        return view('productLocation.create');
    }

    public function show($id): JsonResponse
    {
        $data = $this->productLocationService->getById($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit(): View
    {
        return view();
    }

    public function update($id, ProductLocationRequest $productLocationRequest)
    {
        try {
            DB::transaction(function () use ($id, $productLocationRequest) {
                $input = $productLocationRequest->validated();

                DB::commit();
            });
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function delete($id): JsonResponse
    {
        $this->productLocationService->getById($id)->delete();
        return response()->json(['message' => 'Data has been found!'], 200);
    }

    public function table()
    {
        return $this->productLocationService->table();
    }

    public function select(Request $request)
    {
        $term = $request->input('term');
        return $this->productLocationService->select($term);
    }

    public function selectWithParam(Request $request)
    {
        $term = $request->input('term');
        $productId = $request->input('data');
        return $this->productLocationService->selectWithParam($term, $productId);
    }
}
