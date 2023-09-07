<?php

namespace App\Listeners;

use App\Events\ProductQuantityLow;
use App\Notifications\NewNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendProductQuantityNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductQuantityLow $event): void
    {
        if ($event->product->amount / $event->product->max_amount < $event->percentage / 100) {
            // Kirim notifikasi melalui WebSocket
            event(new NewNotification("Product quantity is low", "Product: {$event->product->name} has quantity below {$event->percentage}%"));

            // Contoh notifikasi lain seperti email, SMS, push notification
            Notification::route('mail', 'email@example.com')
            ->notify(new NewNotification("Product quantity is low", "Product: {$event->product->name} has quantity below {$event->percentage}%"));
        }
    }
}
