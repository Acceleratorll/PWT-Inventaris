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
            $products = Product::where('total_amount <= minimal_amount')->get();

            foreach ($products as $product) {
                
            }

            sleep(60);
        }
    }
}
