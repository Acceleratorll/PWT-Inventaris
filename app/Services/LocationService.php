<?php

namespace App\Services;

use App\Repositories\LocationRepository;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class LocationService
{
    protected $repository;

    public function __construct(LocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store($data)
    {
        $this->repository->create($data);
    }

    public function update($id, $data)
    {
        $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        $this->repository->delete($id);
    }

    public function table()
    {
        $datas = $this->repository->all();

        return DataTables::of($datas)
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('location', function ($data) {
                return $data->location;
            })
            ->addColumn('desc', function ($data) {
                return $data->desc ?? 'NaN';
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d-m-Y');
            })
            ->addColumn('updated_at', function ($data) {
                return $data->updated_at->format('d-m-Y');
            })
            ->addColumn('action', 'partials.button-table.location-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
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

    public function selectByProduct($product_id): JsonResponse
    {
        $datas = $this->repository->getByProduct($product_id);
        $formattedDatas = $datas->map(function ($data) {
            return [
                'id' => $data->id,
                'text' => $data->name,
            ];
        });
        return response()->json($formattedDatas);
    }
}
