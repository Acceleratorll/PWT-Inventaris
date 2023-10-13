<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'PT. Tsubasa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT. Nusa Bangsa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT. Prim A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fajrul',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('suppliers')->insert($data);
    }
}
