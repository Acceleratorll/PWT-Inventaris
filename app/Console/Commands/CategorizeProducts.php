<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CategorizeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:categorize-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Categorize products based on usage duration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Product::where('updated_at', '>=', now()->subYears(1))
            ->update(['category_product_id' => 1]);

        Product::where('updated_at', '>=', now()->subYears(2))
            ->where('updated_at', '<', now()->subYears(1))
            ->update(['category_product_id' => 2]);

        Product::where('updated_at', '<', now()->subYears(2))
            ->update(['category_product_id' => 3]);

        $this->info('Products categorized successfully.');
    }
}
