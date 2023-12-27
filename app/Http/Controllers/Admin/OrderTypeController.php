<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderType;
use App\Services\OrderTypeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderTypeController extends Controller
{
    protected $orderTypeService;

    public function __construct(
        OrderTypeService $orderTypeService,
    ) {
        $this->orderTypeService = $orderTypeService;
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
        $data = $this->orderTypeService->getById($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit(): View
    {
        return view();
    }

    public function delete($id): JsonResponse
    {
        $this->orderTypeService->getById($id)->delete();
        return response()->json(['message' => 'Data has been found!'], 200);
    }

    public function select(Request $request)
    {
        $term = $request->term;
        return $this->orderTypeService->select($term);
    }
}
