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
    protected $labels;
    protected $datas;

    public function __construct($chart, $labels, $datas)
    {
        $this->chart = $chart;
        $this->labels = $labels;
        $this->datas = $datas;
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
}
