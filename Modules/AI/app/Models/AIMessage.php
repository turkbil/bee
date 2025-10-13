<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AI Message Model
 *
 * CENTRAL DATABASE - Tüm tenant'ların mesajları burada
 * conversation üzerinden tenant_id'ye erişilir
 */
class AIMessage extends Model
{
    protected $table = 'ai_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tokens',
        'prompt_tokens',
        'completion_tokens',
        'model_used',
        'processing_time_ms',
        'metadata',
        'message_type',
        'model',
        'tokens_used',
        'context_data',
    ];

    protected $casts = [
        'tokens' => 'integer',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'tokens_used' => 'integer',
        'processing_time_ms' => 'integer',
        'metadata' => 'array',
        'context_data' => 'array',
    ];

    protected $attributes = [
        'message_type' => 'normal',
        'tokens' => 0,
        'prompt_tokens' => 0,
        'completion_tokens' => 0,
        'processing_time_ms' => 0,
    ];

    /**
     * Get the conversation that owns the message
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AIConversation::class);
    }

    /**
     * Scope for user messages
     */
    public function scopeUser($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope for assistant messages
     */
    public function scopeAssistant($query)
    {
        return $query->where('role', 'assistant');
    }

    /**
     * Scope for specific message type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('message_type', $type);
    }

    /**
     * Scope for conversation
     */
    public function scopeForConversation($query, int $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Check if message is from user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if message is from assistant
     */
    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Get total tokens (prompt + completion)
     */
    public function getTotalTokensAttribute(): int
    {
        return $this->tokens ?: ($this->prompt_tokens + $this->completion_tokens);
    }

    /**
     * Get processing time in seconds
     */
    public function getProcessingTimeSecondsAttribute(): float
    {
        return round($this->processing_time_ms / 1000, 2);
    }

    /**
     * Get formatted role
     */
    public function getFormattedRoleAttribute(): string
    {
        return match($this->role) {
            'user' => 'Kullanıcı',
            'assistant' => 'Asistan',
            'system' => 'Sistem',
            default => ucfirst($this->role)
        };
    }

    /**
     * Get short content preview
     */
    public function getPreviewAttribute(): string
    {
        return \Illuminate\Support\Str::limit($this->content, 100);
    }

    /**
     * Update conversation after creating message
     */
    protected static function booted()
    {
        static::created(function (AIMessage $message) {
            // Update conversation's last message time and count
            if ($message->conversation) {
                $message->conversation->incrementMessageCount();

                // Add tokens to conversation total
                if ($message->total_tokens > 0) {
                    $message->conversation->addTokens($message->total_tokens);
                }
            }
        });
    }

    /**
     * Get message details
     */
    public function getDetails(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'formatted_role' => $this->formatted_role,
            'content' => $this->content,
            'preview' => $this->preview,
            'tokens' => [
                'total' => $this->total_tokens,
                'prompt' => $this->prompt_tokens,
                'completion' => $this->completion_tokens,
            ],
            'model_used' => $this->model_used ?? $this->model,
            'processing_time' => [
                'ms' => $this->processing_time_ms,
                'seconds' => $this->processing_time_seconds,
            ],
            'message_type' => $this->message_type,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
