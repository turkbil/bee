<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Tenant;

/**
 * AI Conversation Model
 *
 * CENTRAL DATABASE - Tüm tenant'ların konuşmaları burada
 * tenant_id ile filtreleme yapılır (Global Scope YOK)
 */
class AIConversation extends Model
{
    protected $table = 'ai_conversations';

    protected $fillable = [
        'title',
        'type',
        'feature_name',
        'is_demo',
        'user_id',
        'tenant_id',
        'prompt_id',
        'session_id',
        'total_tokens_used',
        'metadata',
        'status',
        'feature_slug',
        'context_data',
        'is_active',
        'last_message_at',
        'message_count',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
        'is_active' => 'boolean',
        'total_tokens_used' => 'integer',
        'message_count' => 'integer',
        'metadata' => 'array',
        'context_data' => 'array',
        'last_message_at' => 'datetime',
    ];

    protected $attributes = [
        'type' => 'chat',
        'status' => 'active',
        'is_demo' => false,
        'is_active' => true,
        'total_tokens_used' => 0,
        'message_count' => 0,
    ];

    /**
     * Get the user that owns the conversation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tenant that owns the conversation
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the prompt used for this conversation
     */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    /**
     * Get all messages for this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AIMessage::class, 'conversation_id');
    }

    /**
     * Scope for specific tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope for active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('is_active', true);
    }

    /**
     * Scope for archived conversations
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific feature
     */
    public function scopeForFeature($query, string $featureName)
    {
        return $query->where('feature_name', $featureName);
    }

    /**
     * Scope for session ID
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Archive this conversation
     */
    public function archive(): bool
    {
        $this->status = 'archived';
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Restore archived conversation
     */
    public function unarchive(): bool
    {
        $this->status = 'active';
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Add tokens to total usage
     */
    public function addTokens(int $tokens): void
    {
        $this->increment('total_tokens_used', $tokens);
    }

    /**
     * Increment message count
     */
    public function incrementMessageCount(): void
    {
        $this->increment('message_count');
        $this->last_message_at = now();
        $this->save();
    }

    /**
     * Get conversation title or generate one
     */
    public function getTitleAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }

        // Generate title from first message
        $firstMessage = $this->messages()->where('role', 'user')->first();
        if ($firstMessage) {
            return \Illuminate\Support\Str::limit($firstMessage->content, 50);
        }

        return 'Yeni Sohbet';
    }

    /**
     * Get formatted type
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->type) {
            'chat' => 'Sohbet',
            'feature_test' => 'Özellik Testi',
            'admin_chat' => 'Admin Sohbet',
            default => ucfirst($this->type)
        };
    }

    /**
     * Check if conversation is from guest user
     */
    public function isGuest(): bool
    {
        return empty($this->user_id);
    }

    /**
     * Get conversation summary
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'formatted_type' => $this->formatted_type,
            'feature_name' => $this->feature_name,
            'message_count' => $this->message_count,
            'total_tokens' => $this->total_tokens_used,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'last_message_at' => $this->last_message_at?->toIso8601String(),
        ];
    }
}
