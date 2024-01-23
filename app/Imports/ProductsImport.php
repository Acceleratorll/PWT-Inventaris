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
        $materialName = $row['bahan'];
        $material = Material::where("name", "like", "%" . $materialName . "%")->first();
        $materialId = $material ? $material->id : null;

        // Retrieve the product type ID
        $type = ProductType::where("name", "like", "%" . $row['tipe_barang'] . "%")->first();
        $typeId = $type ? $type->id : null;

        // Retrieve the qualifier ID
        $qualifier = Qualifier::where("name", "like", "%" . $row['satuan'] . "%")->first();
        $qualifierId = $qualifier ? $qualifier->id : null;

        // Retrieve the category product ID
        $category = CategoryProduct::where("name", "like", "%" . $row['kategori'] . "%")->first();
        $categoryProductId = $category ? $category->id : null;

        return new Product([
            'name' => $row['nama'],
            'product_code' => $row['kode'],
            'total_amount' => $row['stock'],
            'minimal_amount' => $row['stock_minimal'],
            'note' => $row['keterangan'],
            'material_id' => $materialId,
            'product_type_id' => $typeId,
            'qualifier_id' => $qualifierId,
            'category_product_id' => $categoryProductId,
        ]);
    }
}
