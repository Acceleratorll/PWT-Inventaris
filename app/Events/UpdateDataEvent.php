<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateDataEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $data;
    protected $name;

    public function __construct($data, $name)
    {
        $this->data = $data;
        $this->name = $name;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('public.update.data.1'),
        ];
    }

    public function broadcastAs()
    {
        return 'update.data';
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->data,
            'name' => $this->name
        ];
    }
}
