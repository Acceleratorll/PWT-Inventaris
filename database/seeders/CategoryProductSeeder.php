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
                'max' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Slow Moving',
                'max' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unused',
                'max' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('category_products')->insert($data);
    }
}
