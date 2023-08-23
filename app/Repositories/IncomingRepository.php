<?php

namespace App\Repositories;

use App\Models\Incoming;

class IncomingRepository
{
    protected $model;

    public function __construct(Incoming $model)
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
            ->where('code', 'LIKE', '%' . $term . '%')
            ->orWhereHas('incoming_products', function ($query) use ($term) {
                $query->whereHas('product', function ($query) use ($term) {
                    $query->where('name', 'LIKE', '%' . $term . '%');
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
        return $this->model->with('incoming_products')->paginate(10);
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
