<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualifierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'unit_group_id' => '1',
                'name' => 'Millimeter',
                'abbreviation' => 'mm',
                'conversion_factor' => '1000',
            ],
            [
                'unit_group_id' => '1',
                'name' => 'Centimeter',
                'abbreviation' => 'cm',
                'conversion_factor' => '100',
            ],
            [
                'unit_group_id' => '1',
                'name' => 'Meter',
                'abbreviation' => 'm',
                'conversion_factor' => '1',
            ],
            [
                'unit_group_id' => '1',
                'name' => 'Kilometer',
                'abbreviation' => 'km',
                'conversion_factor' => '0.001',
            ],
            [
                'unit_group_id' => '2',
                'name' => 'Milligram',
                'abbreviation' => 'mg',
                'conversion_factor' => '1000',
            ],
            [
                'unit_group_id' => '2',
                'name' => 'Gram',
                'abbreviation' => 'g',
                'conversion_factor' => '1',
            ],
            [
                'unit_group_id' => '2',
                'name' => 'Kilogram',
                'abbreviation' => 'kg',
                'conversion_factor' => '0.001',
            ],
            [
                'unit_group_id' => '2',
                'name' => 'Ton',
                'abbreviation' => 'ton',
                'conversion_factor' => '0.000001',
            ],
            [
                'unit_group_id' => '3',
                'name' => 'Lembar',
                'abbreviation' => 'lbr',
                'conversion_factor' => '1',
            ],
            [
                'unit_group_id' => '3',
                'name' => 'Pack',
                'abbreviation' => 'pack',
                'conversion_factor' => '10',
            ],
            [
                'unit_group_id' => '3',
                'name' => 'Kardus',
                'abbreviation' => 'kardus',
                'conversion_factor' => '100',
            ],
            [
                'unit_group_id' => '3',
                'name' => 'Rims',
                'abbreviation' => 'rim',
                'conversion_factor' => '500',
            ],
        ];

        DB::table('qualifiers')->insert($data);
    }
}
