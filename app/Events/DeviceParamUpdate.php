<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeviceParamUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $deviceParam;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($deviceParam)
    {
        $this->deviceParam = $deviceParam;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('device-param-data');
    }

    public function broadcastWith()
    {
        return ['deviceParam' => $this->deviceParam];
    }
}
