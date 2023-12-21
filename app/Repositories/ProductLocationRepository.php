<?php

namespace App\Repositories;

use App\Models\ProductLocation;

class ProductLocationRepository
{
    protected $model;

    public function __construct(ProductLocation $model)
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
            ->with('location', 'product')
            ->where('amount', 'LIKE', '%' . $term . '%')
            ->orWhereHas('location', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('location', 'LIKE', '%' . $term . '%')
                    ->orWhere('desc', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('product', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('product_code', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function allByProduct($data)
    {
        return $this->model
            ->with('location', 'product')
            ->where('product_id', $data)
            ->get();
    }

    public function getByMonth($month)
    {
        return $this->model
            ->with(['product.product_transaction.transaction.supplier'])
            ->whereMonth('created_at', $month)
            ->get();
    }

    public function all()
    {
        return $this->model->with([
            'product.material',
            'product.qualifier',
            'product.category_product',
            'product.product_type',
        ])->get();
    }

    public function paginate(int $num)
    {
        return $this->model->with([
            'product.material',
            'product.qualifier',
            'product.category_product',
            'product.product_type',
        ])->paginate($num);
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
