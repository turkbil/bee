<?php

namespace Modules\AI\app\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranslationCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $sessionId,
        public string $entityType,
        public int $entityId,
        public array $result
    ) {}

    public function broadcastOn()
    {
        return new Channel('translation.' . $this->sessionId);
    }

    public function broadcastAs()
    {
        return 'translation.completed';
    }

    public function broadcastWith()
    {
        return [
            'session_id' => $this->sessionId,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'result' => $this->result,
            'timestamp' => now()->toISOString()
        ];
    }
}