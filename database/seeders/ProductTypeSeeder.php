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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bahan Bantu',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bahan Tinta',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barang Jadi Beli',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barang Jadi Produksi',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barang Langsung Biaya',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('product_types')->insert($data);
    }
}
