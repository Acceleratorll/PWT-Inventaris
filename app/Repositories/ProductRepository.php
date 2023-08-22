<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
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
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('product_code', 'LIKE', '%' . $term . '%')
            ->orWhere('amount', 'LIKE', '%' . $term . '%')
            ->orWhere('note', 'LIKE', '%' . $term . '%')
            ->orWhereHas('material', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('qualifier', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('product_type', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate()
    {
        return $this->model->with('product_type', 'qualifier', 'material')->paginate(10);
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
