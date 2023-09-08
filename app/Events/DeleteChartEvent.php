<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteChartEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $chart;
    protected $label;

    public function __construct($chart, $label)
    {
        $this->chart = $chart;
        $this->label = $label;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('public.delete.chart.1'),
        ];
    }

    public function broadcastAs()
    {
        return 'delete.chart';
    }

    public function broadcastWith(): array
    {
        return [
            'chart' => $this->chart,
            'label' => $this->label,
        ];
    }
}
