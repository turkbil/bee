<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\User;
use Modules\AI\App\Services\AICreditService;

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
        'credit_used', // alias for credits_used for consistency
        'purchase_id', // to track which purchase these credits came from
        'prompt_credits',
        'completion_credits',
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
        'credits_used' => 'float',
        'credit_used' => 'float', // alias
        'prompt_credits' => 'float',
        'completion_credits' => 'float',
        'cost_multiplier' => 'decimal:4',
        'response_metadata' => 'array',
        'metadata' => 'array',
        'used_at' => 'datetime',
        'purchase_id' => 'integer'
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
     * Get formatted credits used
     */
    public function getFormattedCreditsUsedAttribute(): string
    {
        return format_credit($this->credits_used);
    }

    /**
     * Calculate cost for this usage
     */
    public function getCalculatedCostAttribute(): float
    {
        return $this->credits_used * ($this->cost_multiplier ?? 1.0);
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
     * Get the credit purchase this usage came from
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(AICreditPurchase::class, 'purchase_id');
    }

    /**
     * Accessor for credit_used (alias for credits_used)
     */
    public function getCreditUsedAttribute(): float
    {
        return (float) ($this->attributes['credit_used'] ?? $this->attributes['credits_used'] ?? 0);
    }

    /**
     * Mutator for credit_used (alias for credits_used)
     */
    public function setCreditUsedAttribute($value): void
    {
        $this->attributes['credit_used'] = $value;
        $this->attributes['credits_used'] = $value; // Keep both in sync
    }

    /**
     * Calculate efficiency ratio for this usage
     */
    public function getEfficiencyRatioAttribute(): float
    {
        if (!$this->purchase || $this->purchase->cost_per_credit <= 0) {
            return 1.0;
        }
        
        $marketAverage = app(AICreditService::class)
            ->calculateMarketAverageCostPerCredit();
            
        if ($marketAverage <= 0) {
            return 1.0;
        }
        
        return $this->purchase->cost_per_credit / $marketAverage;
    }

    /**
     * Get actual cost for this specific usage
     */
    public function getActualCostAttribute(): float
    {
        if (!$this->purchase) {
            return $this->credit_used * 0.01; // Fallback cost
        }
        
        return $this->credit_used * $this->purchase->cost_per_credit;
    }

    /**
     * Get time elapsed since usage
     */
    public function getTimeElapsedAttribute(): string
    {
        if (!$this->used_at) {
            return 'Unknown';
        }
        
        return $this->used_at->diffForHumans();
    }

    /**
     * Get usage performance score (0-100)
     */
    public function getPerformanceScoreAttribute(): float
    {
        $baseScore = 50; // Neutral score
        
        // Factor in efficiency (lower cost per credit = higher score)
        $efficiencyScore = max(0, min(50, (2 - $this->efficiency_ratio) * 25));
        
        // Factor in response time/size ratio if available
        $responseScore = 0;
        if (isset($this->response_metadata['response_time']) && isset($this->response_metadata['token_count'])) {
            $tokensPerSecond = $this->response_metadata['token_count'] / max(1, $this->response_metadata['response_time']);
            $responseScore = min(50, $tokensPerSecond / 10); // Scale tokens per second
        }
        
        return min(100, $baseScore + $efficiencyScore + $responseScore);
    }

    /**
     * Scope for high-cost usages
     */
    public function scopeHighCost($query, float $threshold = 10.0)
    {
        return $query->where('credit_used', '>', $threshold);
    }

    /**
     * Scope for recent usages (last N days)
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('used_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific model
     */
    public function scopeForModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Get detailed usage analytics
     */
    public function getDetailedAnalytics(): array
    {
        return [
            'basic_info' => [
                'id' => $this->id,
                'credits_used' => $this->credit_used,
                'actual_cost' => $this->actual_cost,
                'used_at' => $this->used_at?->toISOString(),
                'time_elapsed' => $this->time_elapsed,
            ],
            'efficiency_metrics' => [
                'efficiency_ratio' => $this->efficiency_ratio,
                'performance_score' => $this->performance_score,
                'cost_per_credit' => $this->purchase?->cost_per_credit ?? 0,
            ],
            'technical_details' => [
                'provider' => $this->provider_name,
                'model' => $this->model,
                'feature' => $this->feature_slug,
                'usage_type' => $this->usage_type,
                'cost_multiplier' => $this->cost_multiplier,
            ],
            'response_data' => $this->response_metadata ?? [],
        ];
    }

    /**
     * Get usage statistics by provider
     */
    public static function getProviderStats($tenantId = null, $dateRange = null): \Illuminate\Support\Collection
    {
        $query = self::with(['aiProvider', 'purchase'])
            ->selectRaw('
                ai_provider_id,
                provider_name,
                COUNT(*) as total_requests,
                SUM(COALESCE(credit_used, credits_used)) as total_credits,
                AVG(COALESCE(credit_used, credits_used)) as avg_credits_per_request,
                SUM(COALESCE(credit_used, credits_used) * COALESCE(cost_multiplier, 1)) as total_cost
            ')
            ->groupBy('ai_provider_id', 'provider_name');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($dateRange && is_array($dateRange) && count($dateRange) === 2) {
            $query->whereBetween('used_at', $dateRange);
        }

        return $query->get();
    }

    /**
     * Get usage trend analysis
     */
    public static function getTrendAnalysis(
        ?int $tenantId = null,
        int $days = 30
    ): array {
        $startDate = now()->subDays($days);
        $endDate = now();
        
        $query = self::whereBetween('used_at', [$startDate, $endDate]);
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        $dailyUsage = $query->selectRaw('
                DATE(used_at) as date,
                SUM(COALESCE(credit_used, credits_used)) as daily_credits,
                COUNT(*) as daily_requests,
                AVG(COALESCE(credit_used, credits_used)) as avg_credits_per_request
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $totalCredits = $dailyUsage->sum('daily_credits');
        $totalRequests = $dailyUsage->sum('daily_requests');
        $avgDaily = $totalCredits / max(1, $days);
        
        return [
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'days' => $days,
            ],
            'totals' => [
                'total_credits' => $totalCredits,
                'total_requests' => $totalRequests,
                'avg_daily_credits' => $avgDaily,
                'avg_credits_per_request' => $totalCredits / max(1, $totalRequests),
            ],
            'daily_breakdown' => $dailyUsage->toArray(),
            'trend_indicators' => [
                'is_increasing' => $dailyUsage->count() > 1 && 
                    $dailyUsage->last()->daily_credits > $dailyUsage->first()->daily_credits,
                'growth_rate' => $dailyUsage->count() > 1 ? 
                    (($dailyUsage->last()->daily_credits - $dailyUsage->first()->daily_credits) / 
                     max(1, $dailyUsage->first()->daily_credits)) * 100 : 0,
            ],
        ];
    }
}