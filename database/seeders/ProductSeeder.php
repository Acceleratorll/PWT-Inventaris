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
                'max_amount' => '500',
                'amount' => '250',
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
                'max_amount' => '500',
                'amount' => '400',
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
                'max_amount' => '500',
                'amount' => '400',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 3,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114301012',
                'name' => 'T.INVISIBLE BLUE FLUORESCENT',
                'max_amount' => '500',
                'amount' => '400',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 3,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114301021',
                'name' => 'SICPAOASIS RED/GREEN',
                'max_amount' => '500',
                'amount' => '400',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 3,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114301024',
                'name' => 'T.VTB 21 BLACK LUMINESCENT RED (3P7056SP)',
                'max_amount' => '500',
                'amount' => '400',
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
                'max_amount' => '500',
                'amount' => '400',
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
                'max_amount' => '500',
                'amount' => '400',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 3,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114301033',
                'name' => 'T.PENOMORAN',
                'max_amount' => '500',
                'amount' => '400',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 3,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114303003',
                'name' => 'T.3005 REFLEX BLUE',
                'max_amount' => '500',
                'amount' => '400',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_id' => 3,
                'product_type_id' => 3,
                'qualifier_id' => 7,
                'category_product_id' => 1,
                'product_code' => '114303011',
                'name' => 'T.OS 978 S STT NAS L BLUE',
                'max_amount' => '500',
                'amount' => '400',
                'note' => 'Noted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($data);

        Product::factory()
            ->count(50)
            ->create();
    }
}
