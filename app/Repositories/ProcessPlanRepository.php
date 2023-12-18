<?php

namespace App\Repositories;

use App\Models\Material;
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
        return $this->model
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereHas('outgoing_products.product_transaction_location.product.material')
            ->get();
    }

    public function qtyCurrentMonth($year)
    {
        $datas = ProcessPlan::whereYear('created_at', $year)->get();
        $types = Material::all(); // Assuming ProductType is a model for your product types.

        $datasets = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($types as $type) {
            $data = [];
            foreach ($months as $index => $month) {
                $totalQty = $datas->filter(function ($item) use ($index, $type) {
                    return $item->created_at->month == $index + 1 &&
                        $item->outgoing_products->contains('product.material_id', $type->id);
                })->sum(function ($item) use ($type) {
                    return $item->outgoing_products->where('product.material_id', $type->id)->sum('qty');
                });

                $data[] = $totalQty;
            }

            $datasets[] = [
                'labels' => $months,
                'label' => $type->name,
                'data' => $data,
                'fill' => false,
            ];
        }

        return $datasets;
    }

    public function getByCustomerName($data)
    {
        $current_month = now()->month;
        $current_year = now()->year;
        return $this->model->with('outgoing_products.product_transaction_location.product.qualifier', 'customer')
            ->whereHas('customer', function ($query) use ($data) {
                $query->where('name', $data);
            })
            ->whereMonth('created_at', $current_month)
            ->whereYear('created_at', $current_year)
            ->get();
    }

    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function search($term)
    {
        return $this->model
            ->with('outgoing_products.product_transaction_location.product.qualifier.unit_group', 'customer')
            ->where('customer', 'LIKE', '%' . $term . '%')
            ->orWhere('order_type', 'LIKE', '%' . $term . '%')
            ->orWhere('desc', 'LIKE', '%' . $term . '%')
            ->orWhereHas('outgoing_products', function ($query) use ($term) {
                $query->whereHas('product_transaction_location', function ($query) use ($term) {
                    $query->whereHas('product', function ($query) use ($term) {
                        $query->where('name', 'LIKE', '%' . $term . '%');
                    });
                });
            })
            ->get();
    }

    public function all()
    {
        return $this->model->with('outgoing_products.product_transaction_location.product.qualifier.unit_group', 'customer')->get();
    }

    public function paginate(int $num)
    {
        return $this->model->with('outgoing_products.product_transaction_location.product.qualifier.unit_group', 'customer')->paginate($num);
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
