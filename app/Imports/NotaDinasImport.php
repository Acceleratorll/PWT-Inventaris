<?php

namespace App\Imports;

use App\Models\NotaDinas;
use App\Models\Product;
use App\Models\ProductPlanning;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NotaDinasImport implements ToModel, WithHeadingRow
{
    private $notadinas;

    public function model(array $row)
    {
        $this->notadinas = new NotaDinas([
            'code' => $row['code'],
            'authorized' => $row['authorized'],
            'from_date' => $row['from'],
            'to_date' => $row['to'],
            'desc' => $row['description'] ?? null
        ]);

        $this->notadinas->save();

        return $this->processProductPlannings($row['product_planned']);
    }

    private function processProductPlannings($productPlannings)
    {
        // $pattern = '/([^\[\]]+)\s+\[Requirement:\s+(\d+)\s+(\w+)\]\s+\[Saldo:\s+(\d+)\s+(\w+)\]\s+\[Procurement:\s+(\d+)\]/';
        $pattern = '/(?:,\s*)?([^\[\]]+)\s+\[Requirement:\s+(\d+)\s+(\w+)\]\s+\[Saldo:\s+(\d+)\s+(\w+)\]\s+\[Procurement:\s+(\d+)\]/';

        preg_match_all($pattern, $productPlannings, $matches, PREG_SET_ORDER);
        $processedProductPlannings = [];
        foreach ($matches as $match) {
            $productId = $this->getProductIdByName($match[1]);
            $qty = $match[2];
            $product_amount = $match[4];
            $procurement_amount = $match[6];

            // Create a new ProductPlanning record
            $productPlanning = ProductPlanning::create([
                'nota_dinas_id' => $this->notadinas->id,
                'product_id' => $productId,
                'requirement_amount' => $qty,
                'product_amount' => $product_amount,
                'procurement_plan_amount' => $procurement_amount,
            ]);

            $processedProductPlannings[] = $productPlanning;
        }
        return $processedProductPlannings;
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
