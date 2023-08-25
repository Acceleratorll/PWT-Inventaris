<?php

namespace Database\Factories;

use App\Models\ProcessPlan;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OutgoingProduct>
 */
class OutgoingProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'process_plan_id' => ProcessPlan::factory(),
            'product_id' => Product::inRandomOrder()->first()->id,
            'qty' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
