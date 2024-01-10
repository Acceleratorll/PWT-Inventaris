<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // RoleSeeder::class,
            OrderTypeSeeder::class,
            LocationSeeder::class,
            // NotaDinasSeeder::class,
            MaterialSeeder::class,
            ProductTypeSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            UnitGroupSeeder::class,
            CategoryProductSeeder::class,
            UserSeeder::class,
            QualifierSeeder::class,
            ProductSeeder::class,
            RoleAndPermissionSeeder::class,
            // ProcessPlanSeeder::class,
        ]);
    }
}
