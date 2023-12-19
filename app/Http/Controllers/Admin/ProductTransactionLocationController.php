<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductTransactionLocationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductTransactionLocationController extends Controller
{
    protected $productPlanningService;

    public function __construct(
        ProductTransactionLocationService $productPlanningService,
    ) {
        $this->productPlanningService = $productPlanningService;
    }

    public function index(): View
    {
        return view();
    }

    public function create(): View
    {
        return view();
    }

    public function show($id): JsonResponse
    {
        $data = $this->productPlanningService->getById($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit(): View
    {
        return view();
    }

    public function delete($id): JsonResponse
    {
        $this->productPlanningService->getById($id)->delete();
        return response()->json(['message' => 'Data has been found!'], 200);
    }
}
