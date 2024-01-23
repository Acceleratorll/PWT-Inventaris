<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Supplier;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductTransactionImport implements ToModel, WithHeadingRow
{
    private $productTransaction;

    public function model(array $row)
    {
        $this->productTransaction = Transaction::create([
            'supplier_id' => $this->getSupplierIdByName($row['supplier']),
            'code' => $row['code'],
            'status' => $row['status'] == 'Selesai' ? 1 : 0,
            'purchase_date' => Carbon::parse($row['purchase_date'])->format('Y-m-d'),
            'note' => $row['note'] ?? null,
        ]);

        $this->processIncomingProducts($row['product_transactions']);

        return $this->productTransaction;
    }

    private function processIncomingProducts($incomingProducts)
    {
        preg_match_all('/\s*([^\[\]]+)\s*\[Amount: (\d+) [^\]]+\] \[Saldo Awal: (\d+)\],?/', $incomingProducts, $matches, PREG_SET_ORDER);

        $processedIncomingProducts = [];

        foreach ($matches as $match) {
            $productId = $this->getProductIdByName($match[1]);
            $qty = $match[2];
            $product_amount = $match[3];

            if ($productId !== -1) {
                $incomingProduct = ProductTransaction::create([
                    'transaction_id' => $this->productTransaction->id,
                    'product_id' => $productId,
                    'amount' => $qty,
                    'product_amount' => $product_amount,
                ]);

                $processedIncomingProducts[] = $incomingProduct;
            }
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
};
