<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class MonitorProductQuantity extends Command
{
    protected $signature = 'product:monitor';
    protected $description = 'Monitor product quantity and send notifications when low.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        while (true) {
            $products3 = Product::whereRaw('amount <= (0.3 * max_amount)')->get();
            $products1 = Product::whereRaw('amount <= (0.1 * max_amount)')->get();

            foreach ($products3 as $product) {
                event(new \App\Events\ProductQuantityLow($product, 30));
            }

            foreach ($products1 as $product) {
                event(new \App\Events\ProductQuantityLow($product, 10));
            }

            sleep(60);
        }
    }
}
