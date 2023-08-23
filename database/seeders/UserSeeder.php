<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'role_id' => 1,
                'name' => 'staff',
                'email' => 'staff@mail.com',
                'password' => bcrypt('12345'),
            ],
            [
                'role_id' => 2,
                'name' => 'kadev',
                'email' => 'kadev@mail.com',
                'password' => bcrypt('12345'),
            ],
        ];

        DB::table('users')->insert($data);
    }
}
