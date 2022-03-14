<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TunnelCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $tunnels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $tunnels = [])
    {
        $this->tunnels = $tunnels;
    }

    public function getTunnels()
    {
        return $this->tunnels;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
