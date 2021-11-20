<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportClassSockets implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $report;
    public $class_id;
    public $type;

    public function __construct($report, $class_id, $type)
    {
        $this->report = $report;
        $this->class_id = $class_id;
        $this->type = $type;
    }


    public function broadcastOn()
    {
        $class_id = $this->class_id;
        return new PrivateChannel('class_report.' . $class_id);
    }
}
