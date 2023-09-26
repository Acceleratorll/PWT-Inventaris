<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getJsonRoles(Request $request): JsonResponse
    {
        $term = $request->term;
        $roles = $this->roleRepository->search($term);
        $formattedRoles = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'text' => $role->name,
            ];
        });
        return response()->json($formattedRoles);
    }
}
