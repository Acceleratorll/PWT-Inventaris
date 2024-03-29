<?php

namespace App\Repositories;

use App\Models\Product;
use Carbon\Carbon;

class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->with(
            'product_type',
            'qualifier',
            'material',
            'category_product',
            'product_transactions.transaction',
            'outgoing_products.process_plan',
            'product_plannings',
        )->findOrFail($id);
    }

    public function search($term)
    {
        return $this->model
            ->with(
                'product_type',
                'qualifier',
                'material',
                'category_product',
                'product_transactions',
                'outgoing_products',
                'product_plannings',
            )
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('product_code', 'LIKE', '%' . $term . '%')
            ->orWhere('minimal_amount', 'LIKE', '%' . $term . '%')
            ->orWhere('total_amount', 'LIKE', '%' . $term . '%')
            ->orWhere('note', 'LIKE', '%' . $term . '%')
            ->orWhereHas('material', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('qualifier', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('product_type', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('category_product', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->get();
    }

    public function all()
    {
        return $this->model
            ->with(
                'product_type',
                'qualifier',
                'material',
                'category_product',
                'product_transactions.transaction.supplier',
                'outgoing_products.process_plan.customer',
                'product_plannings',
            )
            ->get();
    }

    public function getThisMonth()
    {
        $currentMonth = Carbon::now()->month;

        return $this->model
            ->with([
                'product_type',
                'qualifier',
                'material',
                'category_product',
                'product_transactions' => function ($query) use ($currentMonth) {
                    $query->whereMonth('updated_at', $currentMonth);
                },
                'product_transactions.transaction.supplier',
                'outgoing_products' => function ($query) use ($currentMonth) {
                    $query->whereMonth('updated_at', $currentMonth);
                },
                'outgoing_products.process_plan.customer',
                'product_plannings',
            ])
            ->get();
    }

    public function getTransactionsForPeriod($startMonth, $endMonth)
    {
        $start = Carbon::parse("first day of $startMonth")->startOfDay();
        $end = Carbon::parse("last day of $endMonth")->endOfDay();

        return $this->model
            ->with([
                'product_transactions' => function ($query) use ($start, $end) {
                    $query->whereBetween('updated_at', [$start, $end]);
                },
                'outgoing_products' => function ($query) use ($start, $end) {
                    $query->whereBetween('updated_at', [$start, $end]);
                },
                'product_type',
                'qualifier',
                'material',
                'category_product',
                'product_transactions.transaction.supplier',
                'outgoing_products.process_plan.customer',
                'product_plannings',
            ])
            ->get();
    }

    public function getByThisYear()
    {
        $currentYear = Carbon::now()->year;

        return $this->model
            ->with(
                'product_type',
                'qualifier',
                'material',
                'category_product',
                'product_transactions.transaction.supplier',
                'outgoing_products.process_plan.customer',
                'product_plannings',
            )
            ->whereHas('product_transactions.transaction', function ($query) use ($currentYear) {
                $query->whereYear('purchase_date', $currentYear);
            })
            ->orWhereHas('outgoing_products', function ($query) use ($currentYear) {
                $query->whereYear('updated_at', $currentYear);
            })
            ->get();
    }

    public function getByCategory($category)
    {
        return $this->model->with(
            'product_type',
            'qualifier',
            'material',
            'category_product',
            'product_transactions.transaction',
            'outgoing_products.process_plan',
            'product_plannings',
        )
            ->whereHas('category_product', function ($query) use ($category) {
                $query->where('name', $category);
            })->take(5)->get();
    }

    public function getWarning()
    {
        return $this->model->with(
            'product_type',
            'qualifier',
            'material',
            'category_product',
            'product_transactions.transaction',
            'outgoing_products.process_plan',
            'product_plannings',
        )->whereRaw('amount < (0.3 * max_amount) && amount > (0.1 * max_amount)')->get();
    }

    public function getDanger()
    {
        return $this->model->with(
            'product_type',
            'qualifier',
            'material',
            'category_product',
            'product_transactions.transaction.supplier',
            'outgoing_products.process_plan.customer',
            'product_plannings',
        )->whereRaw('amount < (0.1 * max_amount)')->get();
    }

    public function orderBy($col, $desc)
    {
        return $this->model->with(
            'product_type',
            'qualifier',
            'material',
            'category_product',
            'product_transactions.transaction',
            'outgoing_products.process_plan',
            'product_plannings',
        )->orderByRaw('CAST(' . $col . ' AS SIGNED) ' . $desc)->get();
    }

    public function paginate(int $number)
    {
        return $this->model->with(
            'product_type',
            'qualifier',
            'material',
            'category_product',
            'product_transactions.transaction',
            'outgoing_products.process_plan',
            'product_plannings',
        )->paginate($number);
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
