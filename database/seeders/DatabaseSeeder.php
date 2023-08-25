<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ProcessPlan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MaterialSeeder::class,
            ProductTypeSeeder::class,
            UnitGroupSeeder::class,
            CategoryProductSeeder::class,
            UserSeeder::class,
            QualifierSeeder::class,
            ProductSeeder::class,
            ProcessPlanSeeder::class,
        ]);
    }
}
