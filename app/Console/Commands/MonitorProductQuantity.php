<?php

namespace App\Console\Commands;

use App\Events\ProductNotificationEvent;
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
        $products = Product::where('total_amount', '<=', 'minimal_amount')->get();
        $users = User::all();

        foreach ($products as $product) {
            foreach ($users as $user) {
                $user->notify(new CriticalProduct($product));
                $notif = $user->unreadNotifications->where('data.type', 'critical')->where('data.type', 'critical')
                    ->sortByDesc('created_at')
                    ->first();;
                }
                event(new ProductNotificationEvent('critical', $product, $notif->data['message']));
        }
    }
}
