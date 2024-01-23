<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Notifications\CriticalProduct;

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
                $users = User::all();
                foreach ($users as $user) {
                    $user->notify(new CriticalProduct($product));
                }
            }

            sleep(60);
        }
    }
}
