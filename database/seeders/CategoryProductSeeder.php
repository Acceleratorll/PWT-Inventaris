<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Daily',
                'min' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Slow Moving',
                'min' => '3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'UnMoved',
                'min' => '7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('category_products')->insert($data);
    }
}
