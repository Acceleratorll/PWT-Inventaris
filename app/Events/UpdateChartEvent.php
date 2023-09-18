<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateChartEvent implements ShouldBroadcast
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
            new Channel('public.update.chart.1'),
        ];
    }

    public function broadcastAs()
    {
        return 'update.chart';
    }

    public function broadcastWith()
    {
        return [
            'chart' => $this->chart,
            'data' => $this->data,
        ];
    }
}
