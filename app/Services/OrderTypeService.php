<?php

namespace App\Services;

use App\Repositories\OrderTypeRepository;

class OrderTypeService
{
    protected $repository;

    public function __construct(OrderTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function select($term)
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
