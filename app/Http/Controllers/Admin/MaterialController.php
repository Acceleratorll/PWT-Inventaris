<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\MaterialRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    protected $materialRepository;

    public function __construct(MaterialRepository $materialRepository)
    {
        $this->materialRepository = $materialRepository;
    }

    public function index(): View
    {
        return view('material.index');
    }

    public function create(): View
    {
        return view('material.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id): View
    {
        $data = $this->materialRepository->find($id);
        return view('material.edit', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function select(Request $request): JsonResponse
    {
        $term = $request->term;
        $materials = $this->materialRepository->search($term);
        $formattedMaterials = $materials->map(function ($material) {
            return [
                'id' => $material->id,
                'text' => $material->name,
            ];
        });
        return response()->json($formattedMaterials);
    }
}
