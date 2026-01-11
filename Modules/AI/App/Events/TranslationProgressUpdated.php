<?php

namespace Modules\AI\app\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranslationProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $sessionId,
        public int $progress,
        public string $message
    ) {}

    public function broadcastOn()
    {
        return new Channel('translation.' . $this->sessionId);
    }

    public function broadcastAs()
    {
        return 'progress.updated';
    }

    public function broadcastWith()
    {
        return [
            'session_id' => $this->sessionId,
            'progress' => $this->progress,
            'message' => $this->message,
            'timestamp' => now()->toISOString()
        ];
    }
}