<?php

namespace App\Repositories;

use App\Models\ProductTransaction;

class ProductTransactionRepository
{
    protected $model;

    public function __construct(ProductTransaction $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->with(['incoming_products'])->findOrFail($id);
    }

    public function getByMonth($month)
    {
        return $this->model
            ->with(['incoming_products.product', 'supplier'])
            ->whereMonth('purchase_date', $month)
            ->get();
    }

    public function getBySupplierName($data)
    {
        $current_month = now()->month;
        $current_year = now()->year;
        return $this->model->with('incoming_products.product.qualifier', 'supplier')
            ->whereHas('supplier', function ($query) use ($data) {
                $query->where('name', $data);
            })
            ->whereMonth('created_at', $current_month)
            ->whereYear('created_at', $current_year)
            ->get();
    }

    public function qtyCurrentMonth($month, $year)
    {
        return $this->model->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereHas('incoming_products.product.material')
            ->count();
    }

    public function search($term)
    {
        return $this->model
            ->where('code', 'LIKE', '%' . $term . '%')
            ->orWhere('purchase_date', 'LIKE', '%' . $term . '%')
            ->orWhereHas('supplier', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('incoming_products', function ($query) use ($term) {
                $query->whereHas('product', function ($subQuery) use ($term) {
                    $subQuery->where('name', 'LIKE', '%' . $term . '%');
                })
                    ->where('qty', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function all()
    {
        return $this->model->with([
            'supplier',
            'incoming_products.product.qualifier',
            'incoming_products.product.material'
        ])->get();
    }

    public function paginate()
    {
        return $this->model->with([
            'supplier',
            'incoming_products.product.qualifier',
            'incoming_products.product.material'
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
