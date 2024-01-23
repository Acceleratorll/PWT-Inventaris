<?php

namespace App\Imports;

use App\Models\CategoryProduct;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Qualifier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Retrieve the material ID
        $materialName = $row['material'];
        $material = Material::where("name", "like", "%" . $materialName . "%")->first();
        $materialId = $material ? $material->id : null;

        // Retrieve the product type ID
        $type = ProductType::where("name", "like", "%" . $row['type'] . "%")->first();
        $typeId = $type ? $type->id : null;

        // Retrieve the qualifier ID
        $qualifier = Qualifier::where("name", "like", "%" . $row['qualifier'] . "%")->first();
        $qualifierId = $qualifier ? $qualifier->id : null;

        // Retrieve the category product ID
        $category = CategoryProduct::where("name", "like", "%" . $row['category'] . "%")->first();
        $categoryProductId = $category ? $category->id : null;

        return new Product([
            'name' => $row['name'],
            'product_code' => $row['product_code'],
            'amount' => $row['amount'],
            'minimal_amount' => $row['minimal_amount'],
            'note' => $row['note'],
            'material_id' => $materialId,
            'product_type_id' => $typeId,
            'qualifier_id' => $qualifierId,
            'category_product_id' => $categoryProductId,
        ]);
    }
}
