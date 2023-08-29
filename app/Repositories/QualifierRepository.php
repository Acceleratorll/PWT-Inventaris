<?php

namespace App\Repositories;

use App\Models\Qualifier;

class QualifierRepository
{
    protected $model;

    public function __construct(Qualifier $model)
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
            ->with('products', 'unit_group')
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('abbreviation', 'LIKE', '%' . $term . '%')
            ->orWhere('conversion_factor', 'LIKE', '%' . $term . '%')
            ->orWhereHas('unit_group', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('desc', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function all()
    {
        return $this->model->with('products', 'unit_group')->get();
    }

    public function paginate()
    {
        return $this->model->with('products', 'unit_group')->paginate(10);
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
