<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\OrderType;
use App\Models\OutgoingProduct;
use App\Models\ProcessPlan;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProcessPlansImport implements ToModel, WithHeadingRow
{
    private $rpp;

    public function model(array $row)
    {
        $this->rpp = new ProcessPlan([
            'customer_id' => $this->getCustomerIdByName($row['customer']),
            'code' => $row['code'],
            'status' => $row['status'],
            'order_type_id' => $this->getOrderTypeIdByName($row['order_type']),
            'desc' => $row['description'] ?? null
        ]);

        $this->rpp->save();

        return $this->processOutgoingProducts($row['outgoing_products']);
    }

    private function processOutgoingProducts($outgoingProducts)
    {
        // $pattern = '/(\S.*?) \[Amount: (\d+) \w+\] \[Saldo Awal: (\d+) \w+\] \[Expired: (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/';

        $pattern = '/(?:, )?(\S.*?) \[Amount: (\d+) \w+\] \[Saldo Awal: (\d+) \w+\] \[Expired: (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/';

        preg_match_all($pattern, $outgoingProducts, $matches, PREG_SET_ORDER);

        $processedOutgoingProducts = [];
        foreach ($matches as $match) {
            $productId = $this->getProductIdByName($match[1]);
            $qty = $match[2];
            $product_amount = $match[3];
            $expired = $match[4];

            // Create a new OutgoingProduct record
            $outgoingProduct = OutgoingProduct::create([
                'process_plan_id' => $this->rpp->id,
                'product_id' => $productId,
                'amount' => $qty,
                'product_amount' => $product_amount,
                'expired' => $expired,
            ]);

            $processedOutgoingProducts[] = $outgoingProduct;
        }
        return $processedOutgoingProducts;
    }

    private function getProductIdByName($productName)
    {
        $product = Product::where('name', $productName)->first();

        if ($product) {
            return $product->id;
        } else {
            return -1;
        }
    }

    private function getOrderTypeIdByName($name)
    {
        $data = OrderType::where('name', $name)->first();

        if ($data) {
            return $data->id;
        } else {
            return -1;
        }
    }

    private function getCustomerIdByName($name)
    {
        $data = Customer::where('name', $name)->first();

        if ($data) {
            return $data->id;
        } else {
            return -1;
        }
    }
}
