<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Staff Gudang',
                'email' => 'staff@mail.com',
                'password' => bcrypt('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kadev Logistik',
                'email' => 'kadevlogistik@mail.com',
                'password' => Hash::make('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PPIC',
                'email' => 'ppic@mail.com',
                'password' => Hash::make('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($data);
    }
}
