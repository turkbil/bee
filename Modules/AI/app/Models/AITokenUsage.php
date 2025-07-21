<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\User;

class AITokenUsage extends Model
{
    protected $table = 'ai_token_usage';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // AI tabloları her zaman central database'de
        $this->setConnection('mysql');
    }
    
    protected $fillable = [
        'tenant_id',
        'user_id',
        'conversation_id',
        'message_id',
        'ai_provider_id',
        'provider_name',
        'tokens_used',
        'prompt_tokens',
        'completion_tokens',
        'usage_type',
        'feature_slug',
        'model',
        'purpose',
        'description',
        'reference_id',
        'cost_multiplier',
        'response_metadata',
        'metadata',
        'used_at'
    ];

    protected $casts = [
        'ai_provider_id' => 'integer',
        'tokens_used' => 'integer',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'cost_multiplier' => 'decimal:4',
        'response_metadata' => 'array',
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
     * Get the AI provider that was used
     */
    public function aiProvider(): BelongsTo
    {
        return $this->belongsTo(\Modules\AI\App\Models\AIProvider::class, 'ai_provider_id');
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

    /**
     * Calculate cost for this usage
     */
    public function getCalculatedCostAttribute(): float
    {
        return $this->tokens_used * ($this->cost_multiplier ?? 1.0);
    }

    /**
     * Scope for specific provider
     */
    public function scopeForProvider($query, $providerId)
    {
        return $query->where('ai_provider_id', $providerId);
    }

    /**
     * Scope for specific feature
     */
    public function scopeForFeature($query, $featureSlug)
    {
        return $query->where('feature_slug', $featureSlug);
    }

    /**
     * Get usage statistics by provider
     */
    public static function getProviderStats($tenantId = null, $dateRange = null)
    {
        $query = self::with('aiProvider')
            ->selectRaw('
                ai_provider_id,
                provider_name,
                COUNT(*) as total_requests,
                SUM(tokens_used) as total_tokens,
                AVG(tokens_used) as avg_tokens_per_request,
                SUM(tokens_used * cost_multiplier) as total_cost
            ')
            ->groupBy('ai_provider_id', 'provider_name');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($dateRange) {
            $query->whereBetween('used_at', $dateRange);
        }

        return $query->get();
    }
}