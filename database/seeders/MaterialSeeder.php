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
            ],
            [
                'name' => 'Bahan Bantu',
                'material_code' => '1131',
                'desc' => '',
            ],
            [
                'name' => 'Bahan Tinta',
                'material_code' => '1143',
                'desc' => '',
            ],
            [
                'name' => 'Barang jadi Beli',
                'material_code' => '1144',
                'desc' => '',
            ],
            [
                'name' => 'Ijazah',
                'material_code' => 'PBJ',
                'desc' => '',
            ],
        ];

        DB::table('materials')->insert($data);
    }
}
