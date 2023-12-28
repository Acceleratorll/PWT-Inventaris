<?php

namespace App\Services;

use App\Repositories\OrderTypeRepository;
use Yajra\DataTables\Facades\DataTables;

class OrderTypeService
{
    protected $repository;

    public function __construct(OrderTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    public function update($id, $data)
    {
        return $this->repository->update($id, $data);
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

    public function table()
    {
        $datas = $this->repository->all();

        return DataTables::of($datas)
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d-m-Y');
            })
            ->addColumn('updated_at', function ($data) {
                return $data->updated_at->format('d-m-Y');
            })
            ->addColumn('action', 'partials.button-table.order-type-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }
}
