<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'material_id' => function () {
                return \App\Models\Material::inRandomOrder()->first()->id;
            },
            'product_type_id' => function () {
                return \App\Models\ProductType::inRandomOrder()->first()->id;
            },
            'qualifier_id' => function () {
                return \App\Models\Qualifier::inRandomOrder()->first()->id;
            },
            'category_product_id' => function () {
                return \App\Models\CategoryProduct::inRandomOrder()->first()->id;
            },
            'product_code' => $this->faker->unique()->bothify('??###'),
            'name' => $this->faker->word,
            'max_amount' => $this->faker->randomNumber(2),
            'amount' => $this->faker->randomNumber(2),
            'note' => $this->faker->sentence,
        ];
    }
}
