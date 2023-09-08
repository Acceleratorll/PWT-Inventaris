<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeletedDataEvent implements ShouldBroadcast
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
            new Channel('public.deleted.data.1'),
        ];
    }

    public function broadcastAs()
    {
        return 'deleted.data';
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->data,
            'name' => $this->name,
        ];
    }
}
