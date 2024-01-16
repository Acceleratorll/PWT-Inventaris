<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    protected $model;

    public function __construct(Role $model)
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
            ->get();
    }

    public function all()
    {
        return $this->model->with('users')->get();
    }

    public function paginate()
    {
        return $this->model->with('users')->paginate(10);
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
