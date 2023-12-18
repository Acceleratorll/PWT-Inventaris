<?php

namespace App\Repositories;

use App\Models\OutgoingProduct;

class OutgoingProductRepository
{
    protected $model;

    public function __construct(OutgoingProduct $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function search($term)
    {
        return $this->model
            ->with('product_transaction_location', 'process_plan')
            ->where('amount', 'LIKE', '%' . $term . '%')
            ->orWhereHas('product_transaction_location', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('process_plan', function ($query) use ($term) {
                $query->where('customer', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function getByRpp($rpp)
    {
        return $this->model
            ->with('product_transaction_location', 'process_plan')
            ->where('process_plan_id', $rpp)
            ->get();
    }

    public function all()
    {
        return $this->model->with('product_transaction_location', 'process_plan')->get();
    }

    public function paginate(int $num)
    {
        return $this->model->with('product_transaction_location', 'process_plan')->paginate($num);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        return $this->model->find($id)->update($data);
    }

    public function delete($id)
    {
        $data = $this->model->find($id);
        return $data->delete();
    }
}
