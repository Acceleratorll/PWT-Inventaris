<?php

namespace App\Console\Commands;

use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Console\Command;

class CategorizeProducts extends Command
{
    protected $signature = 'app:categorize-products';

    protected $description = 'Categorize products based on usage duration';

    public function handle()
    {
        $categories = CategoryProduct::orderBy('min', 'asc')->get();
        foreach ($categories as $category) {
            $products = Product::where('updated_at', '<=', now()->subYears($category->min))->get();
            foreach ($products as $product) {
                $product->update(['category_product_id' => $category->id]);
            }
        }

        $this->info('Products categorized successfully.');
    }
}
