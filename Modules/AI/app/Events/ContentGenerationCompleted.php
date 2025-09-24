<?php

declare(strict_types=1);

namespace Modules\AI\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Content Generation Completed Event
 *
 * AI içerik üretimi başarıyla tamamlandığında fırlatılan event
 */
class ContentGenerationCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $sessionId;
    public string $component;
    public array $result;
    public int $tenantId;
    public ?int $userId;
    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $sessionId,
        ?string $component,
        array $result,
        int $tenantId,
        ?int $userId = null
    ) {
        $this->sessionId = $sessionId;
        $this->component = $component;
        $this->result = $result;
        $this->tenantId = $tenantId;
        $this->userId = $userId;
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
            'success' => true,
            'content_length' => strlen($this->result['content'] ?? ''),
            'credits_used' => $this->result['credits_used'] ?? 0,
            'meta' => $this->result['meta'] ?? [],
            'timestamp' => $this->timestamp
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'content.generation.completed';
    }
}