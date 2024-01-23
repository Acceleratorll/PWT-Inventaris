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
            Product::where('updated_at', '<=', now()->subYears($category->min))
                ->update(['category_product_id' => $category->id]);
        }

        $this->info('Products categorized successfully.');
    }
}
