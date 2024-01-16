<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->with('roles')->find($id);
    }

    public function findGet($id)
    {
        return $this->model->with('roles')->where('id', $id)->get();
    }

    public function search($term)
    {
        return $this->model
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('email', 'LIKE', '%' . $term . '%')
            ->orWhereHas('role', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            });
    }

    public function all()
    {
        return $this->model->with('roles')->get();
    }

    public function paginate(int $number)
    {
        return $this->model->with('roles')->paginate($number);
    }

    public function orderBy($col, $desc)
    {
        return $this->model->with('roles')->orderBy($col, $desc)->get();
    }

    public function create($data)
    {
        return $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function update($id, $data)
    {
        $user = $this->model->findOrFail($id);
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if ($data['password']) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }
        return $user;
    }

    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }
}
