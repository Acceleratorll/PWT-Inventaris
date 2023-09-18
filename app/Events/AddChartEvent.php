<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddChartEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $chart;
    protected $data;

    public function __construct($chart, $data)
    {
        $this->chart = $chart;
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('public.add.chart.1'),
        ];
    }

    public function broadcastAs()
    {
        return 'add.chart';
    }

    public function broadcastWith()
    {
        return [
            'chart' => $this->chart,
            'data' => $this->data,
        ];
    }
}
