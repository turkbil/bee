<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\User;

class AICreditUsage extends Model
{
    protected $table = 'ai_credit_usage';
    
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
        'credits_used',
        'input_tokens',
        'output_tokens',
        'credit_cost',
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
        'credits_used' => 'decimal:4',
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'credit_cost' => 'decimal:4',
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
            'feature_test' => 'Feature Test',
            'prowess_test' => 'Prowess Test',
            'conversation' => 'Konuşma',
            'helper_function' => 'Helper Fonksiyon',
            'bulk_test' => 'Toplu Test',
            'generic' => 'Genel',
            default => ucfirst($this->usage_type)
        };
    }

    /**
     * Get formatted credits used
     */
    public function getFormattedCreditsUsedAttribute(): string
    {
        return number_format($this->credits_used, 4) . ' Kredi';
    }

    /**
     * Get formatted cost
     */
    public function getFormattedCostAttribute(): string
    {
        return 'USD ' . number_format($this->credit_cost, 4);
    }

    /**
     * Calculate total tokens
     */
    public function getTotalTokensAttribute(): int
    {
        return $this->input_tokens + $this->output_tokens;
    }

    /**
     * Get cost per credit
     */
    public function getCostPerCreditAttribute(): float
    {
        return $this->credits_used > 0 ? $this->credit_cost / $this->credits_used : 0;
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
                SUM(credits_used) as total_credits,
                SUM(input_tokens) as total_input_tokens,
                SUM(output_tokens) as total_output_tokens,
                SUM(credit_cost) as total_cost,
                AVG(credits_used) as avg_credits_per_request,
                AVG(credit_cost) as avg_cost_per_request
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

    /**
     * Get usage statistics by feature
     */
    public static function getFeatureStats($tenantId = null, $dateRange = null)
    {
        $query = self::selectRaw('
                feature_slug,
                usage_type,
                COUNT(*) as total_requests,
                SUM(credits_used) as total_credits,
                SUM(credit_cost) as total_cost,
                AVG(credits_used) as avg_credits_per_request
            ')
            ->groupBy('feature_slug', 'usage_type');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($dateRange) {
            $query->whereBetween('used_at', $dateRange);
        }

        return $query->get();
    }

    /**
     * Get daily usage trend
     */
    public static function getDailyTrend($days = 30, $tenantId = null)
    {
        $query = self::selectRaw('
                DATE(used_at) as date,
                COUNT(*) as total_requests,
                SUM(credits_used) as total_credits,
                SUM(credit_cost) as total_cost
            ')
            ->where('used_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->get();
    }

    /**
     * Create a new credit usage record
     */
    public static function createUsage(array $data): self
    {
        $usage = new self();
        $usage->fill($data);
        
        // Otomatik hesaplamalar
        if (!isset($data['used_at'])) {
            $usage->used_at = now();
        }
        
        $usage->save();
        
        return $usage;
    }
}