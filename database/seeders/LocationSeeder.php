<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'K-12',
                'location' => 'Rak Kiri Kotak',
                'desc' => 'Rak Kiri Kotak Kiri Kotak Segitiga Atas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'B-52',
                'location' => 'Rak O -> O -> <- [] A ^',
                'desc' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('locations')->insert($data);
    }
}
