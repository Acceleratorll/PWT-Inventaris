<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    protected $model;

    public function __construct(Customer $model)
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
            ->orWhereHas('process_plans', function ($query) use ($term) {
                $query->where('code', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function orderBy($col, $desc)
    {
        return $this->model->with('process_plans')->orderBy($col, $desc)->get();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate()
    {
        return $this->model->with('process_plans')->paginate(10);
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
