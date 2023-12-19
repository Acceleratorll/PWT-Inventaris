<?php

namespace App\Repositories;

use App\Models\ProductTransactionLocation;

class ProductTransactionLocationRepository
{
    protected $model;

    public function __construct(ProductTransactionLocation $model)
    {
        $this->model = $model;
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function search($term)
    {
        return $this->model
            ->with('location', 'product_transaction')
            ->where('amount', 'LIKE', '%' . $term . '%')
            ->orWhereHas('location', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('location', 'LIKE', '%' . $term . '%')
                    ->orWhere('desc', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function allByTransaction($transaction)
    {
        return $this->model
            ->with('location', 'product_transaction')
            ->where('product_transaction_id', $transaction)
            ->get();
    }

    public function getByMonth($month)
    {
        return $this->model
            ->with(['product_transaction.transaction.supplier'])
            ->whereMonth('created_at', $month)
            ->get();
    }

    public function all()
    {
        return $this->model->with([
            'product_transaction.product.material',
            'product_transaction.product.qualifier',
            'product_transaction.product.category_product',
            'product_transaction.product.product_type',
        ])->get();
    }

    public function paginate(int $num)
    {
        return $this->model->with([
            'product_transaction.product.material',
            'product_transaction.product.qualifier',
            'product_transaction.product.category_product',
            'product_transaction.product.product_type',
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
        $data = $this->model->find($id);
        return $data->delete();
    }
}
