<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CheckProductQuantities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-product-quantities';

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
        $products = Product::where('total_amount', '<=', 'minimal_amount')->get();

        foreach ($products as $product) {
            event(new \App\Events\ProductQuantityLow($product, 50)); // Notifikasi saat kuantitas produk kurang dari 50%
        }
    }
}
