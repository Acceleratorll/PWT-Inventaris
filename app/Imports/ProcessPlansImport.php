<?php

namespace App\Imports;

use App\Models\OutgoingProduct;
use App\Models\ProcessPlan;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProcessPlansImport implements ToModel, WithHeadingRow
{
    private $rpp;

    public function model(array $row)
    {
        $this->rpp = new ProcessPlan([
            'customer' => $row['customer'],
            'code' => $row['code'],
            'order_type' => $row['order_type'],
        ]);

        $this->rpp->save();

        return $this->processOutgoingProducts($row['outgoing_products']);
    }

    private function processOutgoingProducts($outgoingProducts)
    {
        preg_match_all('/\s([^\[\]]+)\s\(Qty:\s(\d+)\s\w+\]/', $outgoingProducts, $matches, PREG_SET_ORDER);


        $processedOutgoingProducts = [];

        foreach ($matches as $match) {


            $productId = $this->getProductIdByName($match[1]);
            $qty = $match[2];

            // Here, you can perform additional validation or data processing as needed
            // For example, you can check if the product with $productName exists in your database
            // If not, you can skip it or handle the error accordingly

            // Create a new OutgoingProduct record
            $outgoingProduct = OutgoingProduct::create([
                'process_plan_id' => $this->rpp->id,
                'product_id' => $productId, // You need to retrieve the product ID based on $productName
                'qty' => $qty,
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
            // dd($productName);
            // dd(OutgoingProduct::with('product')->whereHas('product', function ($query) use ($productName) {
            //     $query->where('name', $productName);
            // })
            //     ->first());
            return -1;
        }
    }
}
