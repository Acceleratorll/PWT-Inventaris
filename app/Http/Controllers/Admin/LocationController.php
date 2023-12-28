<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Services\LocationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(
        LocationService $locationService,
    ) {
        $this->locationService = $locationService;
    }

    public function getLocations()
    {
        return $this->locationService->table();
    }

    public function index(): View
    {
        return view('location.index');
    }

    public function create(): View
    {
        return view('location.create');
    }

    public function store(LocationRequest $locationRequest)
    {
        try {
            $input = $locationRequest->validated();
            DB::transaction(function () use ($input) {
                $this->locationService->store($input);
                DB::commit();
            });
            return redirect()->back()->with('success', 'Location created successfully !');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th);
        }
    }

    public function show($id): JsonResponse
    {
        $data = $this->locationService->getById($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit($id): View
    {
        $data = $this->locationService->getById($id);
        return view('location.edit', compact('data'));
    }

    public function update($id, LocationRequest $locationRequest)
    {
        try {
            $input = $locationRequest->validated();
            DB::transaction(function () use ($input, $id) {
                $this->locationService->update($id, $input);
                DB::commit();
            });
            return redirect()->route('location.index')->with('success', 'Location updated successfully !');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th);
        }
    }

    public function destroy($id): JsonResponse
    {
        $this->locationService->delete($id);
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
