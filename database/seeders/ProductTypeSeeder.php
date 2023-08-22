<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Bahan Baku',
                'desc' => '',
            ],
            [
                'name' => 'Bahan Bantu',
                'desc' => '',
            ],
            [
                'name' => 'Bahan Tinta',
                'desc' => '',
            ],
            [
                'name' => 'Barang Jadi Beli',
                'desc' => '',
            ],
            [
                'name' => 'Barang Jadi Produksi',
                'desc' => '',
            ],
            [
                'name' => 'Barang Langsung Biaya',
                'desc' => '',
            ],
        ];

        DB::table('product_types')->insert($data);
    }
}
