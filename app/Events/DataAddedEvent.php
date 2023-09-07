<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataAddedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $datas;

    public function __construct($datas)
    {
        $this->datas = $datas;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('public.data.added.1'),
        ];
    }

    public function broadcastAs()
    {
        return 'data.added';
    }

    public function broadcastWith()
    {
        return [
            "datas" => $this->datas
        ];
    }
}
