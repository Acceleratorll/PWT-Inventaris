<?php

namespace App\Repositories;

use App\Models\Supplier;

class SupplierRepository
{
    protected $model;

    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    public function orderBy($col, $desc)
    {
        return $this->model->with('product_transactions')->orderBy($col, $desc)->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function search($term)
    {
        return $this->model
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhereHas('product_transactions', function ($query) use ($term) {
                $query->where('code', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate()
    {
        return $this->model->with('product_transactions')->paginate(10);
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
