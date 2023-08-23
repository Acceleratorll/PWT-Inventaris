<?php

namespace App\Repositories;

use App\Models\IncomingProduct;

class IncomingProductRepository
{
    protected $model;

    public function __construct(IncomingProduct $model)
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
            ->where(function ($query) use ($term) {
                $query->where('qty', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('product', function ($query) use ($term) {
                        $query->where('name', 'LIKE', '%' . $term . '%');
                    })
                    ->orWhereHas('incoming', function ($query) use ($term) {
                        $query->where('code', 'LIKE', '%' . $term . '%');
                    });
            })
            ->get();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate()
    {
        return $this->model->with('product', 'incoming')->paginate(10);
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
