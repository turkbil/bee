<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\User;

class AITokenUsage extends Model
{
    use BelongsToTenant;
    protected $table = 'ai_token_usage';
    
    protected $fillable = [
        'tenant_id',
        'user_id',
        'conversation_id',
        'message_id',
        'tokens_used',
        'prompt_tokens',
        'completion_tokens',
        'usage_type',
        'model',
        'purpose',
        'description',
        'reference_id',
        'metadata',
        'used_at'
    ];

    protected $casts = [
        'tokens_used' => 'integer',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'metadata' => 'array',
        'used_at' => 'datetime'
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'used_at'
    ];

    /**
     * Get the tenant that owns the usage
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that owns the usage
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the conversation that this usage belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(\Modules\AI\App\Models\Conversation::class);
    }

    /**
     * Get the message that this usage belongs to
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(\Modules\AI\App\Models\Message::class);
    }

    /**
     * Scope for specific tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope for this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('used_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ]);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('used_at', [$startDate, $endDate]);
    }

    /**
     * Scope for usage type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('usage_type', $type);
    }

    /**
     * Get usage type label
     */
    public function getUsageTypeLabelAttribute(): string
    {
        return match($this->usage_type) {
            'chat' => 'Sohbet',
            'image' => 'Görsel',
            'text' => 'Metin',
            'translation' => 'Çeviri',
            default => ucfirst($this->usage_type)
        };
    }

    /**
     * Get formatted tokens used
     */
    public function getFormattedTokensUsedAttribute(): string
    {
        return number_format($this->tokens_used) . ' Token';
    }
}