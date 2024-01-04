<?php

namespace App\Services;

use App\Repositories\ProductLocationRepository;

class ProductLocationService
{
    protected $repository;

    public function __construct(ProductLocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        $data = $this->repository->find($id);
        if ($data) {
            $data->created_at = (new \DateTime($data->created_at))->format('Y-m-d');
            $data->updated_at = (new \DateTime($data->updated_at))->format('Y-m-d');
            $data->expired = (new \DateTime($data->expired))->format('Y-m-d');
            $data->purchase_date = (new \DateTime($data->purchase_date))->format('Y-m-d');
        }

        return $data;
    }

    // public function getByProduct($id)
    // {
    //     return $this->repository->allByProduct($id);
    // }

    public function select($term)
    {
        $datas = $this->repository->search($term);
        $formattedDatas = $datas->map(function ($data) {
            return [
                'id' => $data->id,
                'text' => $data->location->name . ', Expired: ' . $data->expired->format('Y-m-d'),
            ];
        });
        return response()->json($formattedDatas);
    }

    public function selectWithParam($term, $param)
    {
        $datas = $this->repository->searchAfterFilter($term, $param);
        $formattedDatas = $datas->map(function ($data) {
            return [
                'id' => $data->id,
                'text' => $data->location->name . ', Expired: ' . $data->expired->format('Y-m-d'),
            ];
        });
        return response()->json($formattedDatas);
    }
}
