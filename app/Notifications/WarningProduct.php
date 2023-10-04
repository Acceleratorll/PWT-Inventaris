<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WarningProduct extends Notification
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

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Low stock alert: {$this->product->name} is below 30% of max amount.",
            'product_id' => $this->product->id,
            'type' => 'warning',
        ];
    }


    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "Low stock alert: {$this->product->name} is below 30% of max amount.",
            'product_id' => $this->product->id,
        ]);
    }

    public function toHtml($notifiable)
    {
        return view('notifications.critical_product', ['product' => $this->product]);
    }
}
