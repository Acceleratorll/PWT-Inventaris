<?php

namespace App\Repositories;

use App\Models\Location;

class LocationRepository
{
    protected $model;

    public function __construct(Location $model)
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
            ->orWhere('location', 'LIKE', '%' . $term . '%')
            ->orWhere('desc', 'LIKE', '%' . $term . '%')
            ->get();
    }

    public function all()
    {
        return $this->model->with('product_transaction_locations')->get();
    }

    public function paginate(int $num)
    {
        return $this->model->with('product_transaction_locations')->paginate($num);
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
