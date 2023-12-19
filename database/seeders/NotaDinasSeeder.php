<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotaDinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Ijazah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Transcrip',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rapot',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('nota_dinas')->insert($data);
    }
}
