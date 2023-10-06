<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CriticalProduct extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    // WarningProduct.php

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Low stock alert: {$this->product->name} is below 10% of max amount.",
            'name' => $this->product->name,
            'id' => $this->product->id,
            'type' => 'critical',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "Low stock alert: {$this->product->name} is below 10% of max amount.",
            'name' => $this->product->name,
            'id' => $this->product->id,
        ]);
    }

    public function toHtml($notifiable)
    {
        return view('notifications.critical_product', ['product' => $this->product]);
    }
}
