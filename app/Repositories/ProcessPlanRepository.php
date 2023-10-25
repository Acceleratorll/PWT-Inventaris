<?php

namespace App\Repositories;

use App\Models\ProcessPlan;

class ProcessPlanRepository
{
    protected $model;

    public function __construct(ProcessPlan $model)
    {
        $this->model = $model;
    }

    public function currentMonth($month, $year)
    {
        return $this->model->whereHas('outgoing_products.product.material')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereHas('outgoing_products.product.material')
            ->get();
    }

    public function qtyCurrentMonth($month, $year)
    {
        return $this->model->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereHas('outgoing_products.product.material')
            ->count();
    }

    public function getByCustomerName($data)
    {
        $current_month = now()->month;
        $current_year = now()->year;
        return $this->model->with('outgoing_products.product.qualifier', 'customer')
            ->whereHas('customer', function ($query) use ($data) {
                $query->where('name', $data);
            })
            ->whereMonth('created_at', $current_month)
            ->whereYear('created_at', $current_year)
            ->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function search($term)
    {
        return $this->model
            ->with('outgoing_products')
            ->where('customer', 'LIKE', '%' . $term . '%')
            ->orWhere('order_type', 'LIKE', '%' . $term . '%')
            ->orWhere('desc', 'LIKE', '%' . $term . '%')
            ->orWhereHas('outgoing_products', function ($query) use ($term) {
                $query->whereHas('product', function ($query) use ($term) {
                    $query->where('name', 'LIKE', '%' . $term . '%');
                });
            })
            ->get();
    }

    public function all()
    {
        return $this->model->with('outgoing_products.product.qualifier.unit_group')->get();
    }

    public function paginate()
    {
        return $this->model->with('outgoing_products')->paginate(10);
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
        return $this->model->findOrFail($id)->delete();
    }
}
