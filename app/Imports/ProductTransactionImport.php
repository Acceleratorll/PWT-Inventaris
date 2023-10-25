<?php

namespace App\Imports;

use App\Models\IncomingProduct;
use App\Models\Product;
use App\Models\productTransaction;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class productTransactionImport implements ToModel, WithHeadingRow
{
    private $productTransaction;

    public function model(array $row)
    {
        $this->productTransaction = new ProductTransaction([
            'supplier_id' => $this->getSupplierIdByName($row['supplier']),
            'code' => $row['code'],
            'purchase_date' => $row['purchase_date'],
            'desc' => $row['description'],
        ]);

        if ($row['description']) {
            $this->productTransaction->desc = $row['description'];
        }

        $this->productTransaction->save();

        return $this->processIncomingProducts($row['incoming_products']);
    }

    private function processIncomingProducts($incomingProducts)
    {
        preg_match_all('/\s([^\[\]]+)\s\(Qty:\s(\d+)\s\w+\]/', $incomingProducts, $matches, PREG_SET_ORDER);


        $processedIncomingProducts = [];

        foreach ($matches as $match) {


            $productId = $this->getProductIdByName($match[1]);
            $qty = $match[2];

            $incomingProduct = IncomingProduct::create([
                'product_transaction_id' => $this->productTransaction->id,
                'product_id' => $productId, // You need to retrieve the product ID based on $productName
                'qty' => $qty,
            ]);

            $processedIncomingProducts[] = $incomingProduct;
        }
        return $processedIncomingProducts;
    }

    private function getSupplierIdByName($supplierName)
    {
        $supplier = Supplier::where('name', $supplierName)->first();
        if ($supplier) {
            return $supplier->id;
        } else {
            return -1;
        }
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
}
