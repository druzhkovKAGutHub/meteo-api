<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeviceConfigEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $key;
    protected $type;
    protected $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($key, $type, $data)
    {
        $this->key  = $key;
        $this->type = $type;
        $this->data = $data;
    }

    public function broadcastAs()
    {
        return 'DeviceConfigEvent';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('device-config.'.$this->key);
    }

    public function broadcastWith()
    {
        return ["type"=>$this->type, "data"=>$this->data];
    }
}
