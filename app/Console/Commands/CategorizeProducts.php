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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::all();

        foreach ($products as $product) {
            $firstUsageDate = $product->first_usage_date; // Replace with actual property name
            $currentDate = now(); // Current date and time

            $usageDuration = $currentDate->diffInYears($firstUsageDate);

            if ($usageDuration < 1) {
                $product->update(['category_product_id' => 1]);
            } elseif ($usageDuration > 1 && $usageDuration <= 2) {
                $product->update(['category_product_id' => 2]);
            } elseif ($usageDuration > 2) {
                $product->update(['category_product_id' => 3]);
            }
        }
    }
}
