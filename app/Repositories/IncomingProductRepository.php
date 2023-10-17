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
            ->with('product', 'incoming')
            ->where('qty', 'LIKE', '%' . $term . '%')
            ->orWhereHas('product', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
                $query->where('product_code', 'LIKE', '%' . $term . '%');
                $query->where('note', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function allByTransaction($transaction)
    {
        return $this->model
            ->with('product', 'product_transaction')
            ->where('product_transaction_id', $transaction)
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

    public function paginate()
    {
        return $this->model->with([
            'product.material',
            'product.qualifier',
            'product.category_product',
            'product.product_type',
        ])->paginate(10);
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
