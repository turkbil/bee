<?php

namespace Modules\AI\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Message Chunk Received Event
 *
 * Streaming AI yanıtları için real-time event
 */
class MessageChunkReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channel;
    public $chunk;

    public function __construct(string $channel, string $chunk)
    {
        $this->channel = $channel;
        $this->chunk = $chunk;
    }

    public function broadcastOn()
    {
        return new Channel($this->channel);
    }

    public function broadcastAs()
    {
        return 'message.chunk';
    }
}
