<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Bahan Kertas',
                'material_code' => '1141',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bahan Bantu',
                'material_code' => '1131',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bahan Tinta',
                'material_code' => '1143',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barang jadi Beli',
                'material_code' => '1144',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ijazah',
                'material_code' => 'PBJ',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('materials')->insert($data);
    }
}
