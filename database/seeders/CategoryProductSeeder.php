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
                'max' => '1',
            ],
            [
                'name' => 'Slow Moving',
                'max' => '2',
            ],
            [
                'name' => 'Daily',
                'max' => '3',
            ],
        ];
        DB::table('category_products')->insert($data);
    }
}
