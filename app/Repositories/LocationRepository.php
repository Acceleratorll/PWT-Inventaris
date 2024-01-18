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

    public function searchAfterFilter($term, $data)
    {
        return $data
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('location', 'LIKE', '%' . $term . '%')
            ->orWhere('desc', 'LIKE', '%' . $term . '%')
            ->orWhereHas('product_locations', function ($q) use ($term) {
                $q->where('product_id', 'LIKE', '%' . $term . '%')
                    ->orWhere('purchase_date', 'LIKE', '%' . $term . '%')
                    ->orWhere('expired', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function getByProduct($product_id)
    {
        return $this->model
            ->orWhereHas('product_locations', function ($q) use ($product_id) {
                $q->where('product_id', $product_id);
            })
            ->get();
    }

    public function all()
    {
        return $this->model->with('product_locations.product.material', 'product_locations.product.qualifier')->get();
    }

    public function paginate(int $num)
    {
        return $this->model->with('product_locations.product.material')->paginate($num);
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
        return $this->model->find($id)->delete();
    }
}
