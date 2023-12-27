<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LocationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(
        LocationService $locationService,
    ) {
        $this->locationService = $locationService;
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
        $data = $this->locationService->getById($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit(): View
    {
        return view();
    }

    public function delete($id): JsonResponse
    {
        $this->locationService->getById($id)->delete();
        return response()->json(['message' => 'Data has been found!'], 200);
    }

    public function selectLocations(Request $request)
    {
        $term = $request->term;
        return $this->locationService->selectLocations($term);
    }

    public function selectByProduct(Request $request)
    {
        $term = $request->term;
        return $this->locationService->selectLocations($term);
    }
}
