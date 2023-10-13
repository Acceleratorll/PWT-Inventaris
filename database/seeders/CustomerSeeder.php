<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Polinema',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'name' => 'Unmer',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'name' => 'ITS',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'name' => 'UB',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'name' => 'UM',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'name' => 'UMM',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'name' => 'Fajrul',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'name' => 'Falah',
                'updated_at' => now(),
                'created_at' => now(),
            ],
        ];

        DB::table('customers')->insert($data);
    }
}
