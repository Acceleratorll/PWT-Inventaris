<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'material_id' => 1,
                'product_type_id' => 1,
                'qualifier_id' => 9,
                'category_product_id' => 1,
                'product_code' => '0001',
                'name' => 'K.SEC. 230 GR (NON FIBER) 65x100',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 1,
                'product_type_id' => 1,
                'qualifier_id' => 10,
                'category_product_id' => 1,
                'product_code' => '0002',
                'name' => 'K.SEC. 230 GR (FIBRE) 65x100',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 1,
                'product_type_id' => 1,
                'qualifier_id' => 10,
                'category_product_id' => 1,
                'product_code' => '114301002',
                'name' => 'K.SEC 96 GR PORTRAIT 24.7 X 35',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 2,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114301012',
                'name' => 'T.INVISIBLE BLUE FLUORESCENT',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 2,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114301021',
                'name' => 'SICPAOASIS RED/GREEN',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 2,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114301024',
                'name' => 'T.VTB 21 BLACK LUMINESCENT RED (3P7056SP)',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 1,
                'product_type_id' => 1,
                'qualifier_id' => 10,
                'category_product_id' => 1,
                'product_code' => '114116065',
                'name' => 'K.NCR BOTTOM WHITE 65X100',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 1,
                'product_type_id' => 1,
                'qualifier_id' => 10,
                'category_product_id' => 1,
                'product_code' => '114116053',
                'name' => 'K.HVS KUNING 80 GR 65 X 100',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 2,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114301033',
                'name' => 'T.PENOMORAN',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 2,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114303003',
                'name' => 'T.3005 REFLEX BLUE',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 2,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114303011',
                'name' => 'T.OS 978 S STT NAS L BLUE',
                'minimal_amount' => '100',
                'total_amount' => '500',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($data);

        // Product::factory()
        //     ->count(50)
        //     ->create();
    }
}
