<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotaDinasService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotaDinasController extends Controller
{
    protected $notaDinasService;

    public function __construct(
        NotaDinasService $notaDinasService,
    ) {
        $this->notaDinasService = $notaDinasService;
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
        $data = $this->notaDinasService->getById($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit(): View
    {
        return view();
    }

    public function delete($id): JsonResponse
    {
        $this->notaDinasService->getById($id)->delete();
        return response()->json(['message' => 'Data has been found!'], 200);
    }
}
