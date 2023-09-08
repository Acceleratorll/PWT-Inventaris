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
    protected $label;
    protected $data;

    public function __construct($chart, $label, $data)
    {
        $this->chart = $chart;
        $this->label = $label;
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
            'label' => $this->label,
            'data' => $this->data,
        ];
    }
}
