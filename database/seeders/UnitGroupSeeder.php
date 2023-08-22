<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Length',
                'desc' => 'Length-Related',
            ],
            [
                'name' => 'Weight',
                'desc' => 'Weight-Related',
            ],
            [
                'name' => 'Sheet',
                'desc' => 'Sheet-Related',
            ],
        ];

        DB::table('unit_groups')->insert($data);
    }
}
