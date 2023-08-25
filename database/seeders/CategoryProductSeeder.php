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
            ],
            [
                'name' => 'Slow Moving',
                'max' => '1',
            ],
            [
                'name' => 'Unused',
                'max' => '2',
            ],
        ];

        DB::table('category_products')->insert($data);
    }
}
