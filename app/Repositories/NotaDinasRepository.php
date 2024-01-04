<?php

namespace App\Repositories;

use App\Models\NotaDinas;

class NotaDinasRepository
{
    protected $model;

    public function __construct(NotaDinas $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->with('product_plannings.product.qualifier')->where('id', $id)->first();
    }

    public function search($term)
    {
        return $this->model
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('code', 'LIKE', '%' . $term . '%')
            ->orWhere('desc', 'LIKE', '%' . $term . '%')
            ->orWhereHas('product_plannings', function ($query) use ($term) {
                $query->orWhereHas('product', function ($q) use ($term) {
                    $q->where('name', 'LIKE', '%' . $term . '%');
                });
            })
            ->get();
    }

    public function all()
    {
        return $this->model->with('product_plannings.product')->get();
    }

    public function paginate(int $num)
    {
        return $this->model->with('product_plannings.product')->paginate($num);
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
        return $this->find($id)->delete();
    }
}
