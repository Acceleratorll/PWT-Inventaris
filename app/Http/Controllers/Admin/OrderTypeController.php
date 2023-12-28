<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderTypeRequest;
use App\Services\OrderTypeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('orderType.index');
    }

    public function create(): View
    {
        return view('orderType.create');
    }

    public function store(OrderTypeRequest $orderTypeRequest)
    {
        try {
            DB::transaction(function () use ($orderTypeRequest) {
                $input = $orderTypeRequest->validated();
                $this->orderTypeService->create($input);
                DB::commit();
            });
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }
    }

    public function show($id): JsonResponse
    {
        $data = $this->orderTypeService->getById($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit($id): View
    {
        $data = $this->orderTypeService->getById($id);
        return view('orderType.edit', compact('data'));
    }

    public function update($id, OrderTypeRequest $orderTypeRequest)
    {
        try {
            DB::transaction(function () use ($id, $orderTypeRequest) {
                $input = $orderTypeRequest->validated();
                $this->orderTypeService->update($id, $input);
                DB::commit();
            });
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id): JsonResponse
    {
        $this->orderTypeService->getById($id)->delete();
        return response()->json(['message' => 'Data has been found!'], 200);
    }

    public function select(Request $request)
    {
        $term = $request->term;
        return $this->orderTypeService->select($term);
    }

    public function table()
    {
        return $this->orderTypeService->table();
    }
}
