<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AI Conversation State Model
 *
 * Tracks conversation state and history for each user session
 * Stores current node position and context data
 */
class AIConversation extends Model
{
    use HasFactory;

    protected $table = 'ai_conversations';

    protected $fillable = [
        'tenant_id',
        'flow_id',
        'current_node_id',
        'session_id',
        'user_id',
        'context_data',
        'state_history',
    ];

    protected $casts = [
        'context_data' => 'array',
        'state_history' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the flow being used
     */
    public function flow(): BelongsTo
    {
        return $this->belongsTo(TenantConversationFlow::class, 'flow_id');
    }

    /**
     * Get messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    /**
     * Scope: Get by session
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope: Get by tenant
     */
    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get or create conversation for session
     */
    public static function getOrCreateForSession(string $sessionId, int $tenantId, ?int $userId = null): self
    {
        $conversation = self::bySession($sessionId)->first();

        if ($conversation) {
            return $conversation;
        }

        // Get active flow for tenant
        $flow = TenantConversationFlow::getActiveFlowForTenant($tenantId);

        if (!$flow) {
            throw new \Exception("No active flow found for tenant {$tenantId}");
        }

        // Create new conversation
        return self::create([
            'tenant_id' => $tenantId,
            'flow_id' => $flow->id,
            'session_id' => $sessionId,
            'user_id' => $userId,
            'current_node_id' => $flow->start_node_id,
            'context_data' => [],
            'state_history' => [],
        ]);
    }

    /**
     * Move to next node
     */
    public function moveToNode(string $nodeId, array $nodeResult = []): void
    {
        // Add to state history
        $history = $this->state_history ?? [];
        $history[] = [
            'node_id' => $this->current_node_id,
            'next_node_id' => $nodeId,
            'timestamp' => now()->toIso8601String(),
            'success' => $nodeResult['success'] ?? true,
        ];

        // Update conversation
        $this->update([
            'current_node_id' => $nodeId,
            'state_history' => $history,
        ]);
    }

    /**
     * Add data to context
     */
    public function addToContext(string $key, $value): void
    {
        $context = $this->context_data ?? [];
        $context[$key] = $value;

        $this->update(['context_data' => $context]);
    }

    /**
     * Merge context data
     */
    public function mergeContext(array $data): void
    {
        $context = array_merge($this->context_data ?? [], $data);
        $this->update(['context_data' => $context]);
    }

    /**
     * Get context value
     */
    public function getContext(string $key, $default = null)
    {
        return $this->context_data[$key] ?? $default;
    }

    /**
     * Get current node from flow
     */
    public function getCurrentNode(): ?array
    {
        if (!$this->current_node_id) {
            return null;
        }

        return $this->flow->getNode($this->current_node_id);
    }

    /**
     * Check if conversation has completed the flow
     */
    public function isCompleted(): bool
    {
        return $this->current_node_id === null;
    }

    /**
     * Reset conversation to start
     */
    public function reset(): void
    {
        $this->update([
            'current_node_id' => $this->flow->start_node_id,
            'context_data' => [],
            'state_history' => [],
        ]);
    }

    /**
     * Get conversation progress percentage
     */
    public function getProgress(): int
    {
        $history = $this->state_history ?? [];
        $totalNodes = count($this->flow->getNodes());

        if ($totalNodes === 0) {
            return 0;
        }

        $visitedNodes = count(array_unique(array_column($history, 'node_id')));

        return (int) round(($visitedNodes / $totalNodes) * 100);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set tenant_id if using tenant context
        static::creating(function ($model) {
            if (!$model->tenant_id && function_exists('tenant')) {
                $model->tenant_id = tenant('id');
            }
        });
    }
}
