<?php

namespace App\Services;

use App\Repositories\ProductLocationRepository;
use Yajra\DataTables\Facades\DataTables;

class ProductLocationService
{
    protected $repository;

    public function __construct(ProductLocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function table($datas)
    {
        return DataTables::of($datas)
            ->addColumn('id', function ($data) {
                return $data->id;
            })
            ->addColumn('product', function ($data) {
                return $data->product->name . ' | ' . $data->product->product_code;
            })
            ->addColumn('location', function ($data) {
                return $data->location->name;
            })
            ->addColumn('amount', function ($data) {
                return $data->amount;
            })
            ->addColumn('purchase_date', function ($data) {
                return $data->purchase_date->format('d-m-Y');
            })
            ->addColumn('expired', function ($data) {
                return $data->expired->format('d-m-Y');
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d-m-Y');
            })
            ->addColumn('updated_at', function ($data) {
                return $data->updated_at->format('d-m-Y');
            })
            // ->addColumn('action', 'partials.button-table.product-location-action')
            // ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
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

    public function update($id, $data)
    {
        return $this->repository->update($id, $data);
    }
}
