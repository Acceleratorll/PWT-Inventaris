<?php

namespace App\Services;

use App\Repositories\LocationRepository;
use Illuminate\Http\JsonResponse;

class LocationService
{
    protected $repository;

    public function __construct(LocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function selectLocations($term): JsonResponse
    {
        $datas = $this->repository->search($term);
        $formattedDatas = $datas->map(function ($data) {
            return [
                'id' => $data->id,
                'text' => $data->name,
            ];
        });
        return response()->json($formattedDatas);
    }
}
