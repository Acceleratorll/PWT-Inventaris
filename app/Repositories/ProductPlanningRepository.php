<?php

namespace App\Repositories;

use App\Models\ProductPlanning;

class ProductPlanningRepository
{
    protected $model;

    public function __construct(ProductPlanning $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->with('product.qualifier')->find($id);
    }

    public function search($term)
    {
        return $this->model
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('desc', 'LIKE', '%' . $term . '%')
            ->orWhereHas('product', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate(int $num)
    {
        return $this->model->with('product')->paginate($num);
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
