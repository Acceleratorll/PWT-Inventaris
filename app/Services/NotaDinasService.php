<?php

namespace App\Services;

use App\Repositories\NotaDinasRepository;
use DateTime;
use Yajra\DataTables\Facades\DataTables;

class NotaDinasService
{
    protected $repository;

    public function __construct(NotaDinasRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    public function update($id, $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function select($term)
    {
        $datas = $this->repository->search($term);
        $formattedDatas = $datas->map(function ($data) {
            return [
                'id' => $data->id,
                'text' => $data->code,
            ];
        });
        return response()->json($formattedDatas);
    }

    public function table()
    {
        $datas = $this->repository->all();

        return DataTables::of($datas)
            ->addColumn('id', function ($data) {
                return $data->id;
            })
            ->addColumn('code', function ($data) {
                return $data->code;
            })
            ->addColumn('period', function ($data) {
                $fromDate = new DateTime($data->from_date);
                $toDate = new DateTime($data->to_date);
                return $fromDate->format('M') . ' - ' . $toDate->format('M');
            })
            ->addColumn('products', function ($data) {
                return $data->product_plannings;
            })
            ->addColumn('desc', function ($data) {
                return $data->desc ?? 'NaN';
            })
            ->addColumn('authorized', function ($data) {
                return $data->authorized;
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d-m-Y');
            })
            ->addColumn('updated_at', function ($data) {
                return $data->updated_at->format('d-m-Y');
            })
            ->addColumn('action', 'partials.button-table.nota-dinas-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }
}
