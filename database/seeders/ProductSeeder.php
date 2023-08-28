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
        ];

        DB::table('products')->insert($data);

        Product::factory()
            ->count(50)
            ->create();
    }
}
