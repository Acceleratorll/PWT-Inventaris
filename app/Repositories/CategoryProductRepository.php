<?php

namespace App\Repositories;

use App\Models\CategoryProduct;

class CategoryProductRepository
{
    protected $model;

    public function __construct(CategoryProduct $model)
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
            ->with('products')
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('max', 'LIKE', '%' . $term . '%')
            ->orWhereHas('products', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function all()
    {
        return $this->model->with('products')->get();
    }

    public function paginate()
    {
        return $this->model->with('products')->paginate(10);
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
        return $this->model->find($id)->delete();
    }
}
