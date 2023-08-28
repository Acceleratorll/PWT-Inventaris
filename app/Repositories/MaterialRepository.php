<?php

namespace App\Repositories;

use App\Models\Material;

class MaterialRepository
{
    protected $model;

    public function __construct(Material $model)
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
            ->orWhere('material_code', 'LIKE', '%' . $term . '%')
            ->orWhere('desc', 'LIKE', '%' . $term . '%')
            ->orWhereHas('products', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('amount', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function all()
    {
        return $this-> model->with('products')->get();
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
        $data = $this->model->find($id);
        $data->update($data);
        return $data;
    }

    public function delete($id)
    {
        $data = $this->model->find($id);
        return $data->delete();
    }
}
