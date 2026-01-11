<?php

declare(strict_types=1);

namespace Modules\AI\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Content Generation Failed Event
 *
 * AI içerik üretimi başarısız olduğunda fırlatılan event
 */
class ContentGenerationFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $sessionId;
    public string $component;
    public string $error;
    public int $tenantId;
    public ?int $userId;
    public bool $finalFailure;
    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $sessionId,
        ?string $component,
        string $error,
        int $tenantId,
        ?int $userId = null,
        bool $finalFailure = false
    ) {
        $this->sessionId = $sessionId;
        $this->component = $component;
        $this->error = $error;
        $this->tenantId = $tenantId;
        $this->userId = $userId;
        $this->finalFailure = $finalFailure;
        $this->timestamp = now()->toISOString();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("ai-content.{$this->tenantId}"),
            new Channel("ai-content.{$this->sessionId}")
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->sessionId,
            'component' => $this->component,
            'success' => false,
            'error' => $this->error,
            'final_failure' => $this->finalFailure,
            'timestamp' => $this->timestamp
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'content.generation.failed';
    }
}