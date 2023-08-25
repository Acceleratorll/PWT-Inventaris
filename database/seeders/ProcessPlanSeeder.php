<?php

namespace Database\Seeders;

use App\Models\OutgoingProduct;
use App\Models\ProcessPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $data = [
        //     [
        //         'name' => 'Length',
        //         'desc' => 'Length-Related',
        //     ],
        //     [
        //         'name' => 'Weight',
        //         'desc' => 'Weight-Related',
        //     ],
        //     [
        //         'name' => 'Sheet',
        //         'desc' => 'Sheet-Related',
        //     ],
        // ];

        // DB::table('process_plans')->insert($data);

        $processPlans = ProcessPlan::factory()
            ->has(OutgoingProduct::factory()->count(2), 'outgoing_products')
            ->count(50)
            ->create();
    }
}
