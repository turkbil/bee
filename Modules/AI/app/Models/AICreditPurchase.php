<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Modules\AI\App\Services\AICreditService;

class AICreditPurchase extends Model
{
    protected $connection = 'central';
    protected $table = 'ai_credit_purchases';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
    }
    
    protected $fillable = [
        'tenant_id',
        'user_id',
        'package_id',
        'credit_amount',
        'price_paid',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_transaction_id',
        'payment_data',
        'notes',
        'purchased_at'
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'amount' => 'decimal:2',
        'credit_amount' => 'integer',
        'payment_data' => 'array',
        'purchased_at' => 'datetime'
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'purchased_at'
    ];

    /**
     * Get the tenant that owns the purchase
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that made the purchase
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package that was purchased
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(\Modules\AI\App\Models\AICreditPackage::class, 'package_id');
    }

    /**
     * Scope for completed purchases
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending purchases
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark purchase as completed
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'purchased_at' => now()
        ]);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_paid, 2) . ' ' . $this->currency;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning', 
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get related credit usages for this purchase
     */
    public function creditUsages(): HasMany
    {
        return $this->hasMany(AICreditUsage::class, 'purchase_id');
    }

    /**
     * Get remaining credits from this purchase
     */
    public function getRemainingCreditsAttribute(): float
    {
        $usedCredits = $this->creditUsages()->sum('credit_used');
        return max(0, $this->credit_amount - $usedCredits);
    }

    /**
     * Get used credits from this purchase
     */
    public function getUsedCreditsAttribute(): float
    {
        return $this->creditUsages()->sum('credit_used');
    }

    /**
     * Check if purchase credits are fully consumed
     */
    public function isFullyConsumed(): bool
    {
        return $this->remaining_credits <= 0;
    }

    /**
     * Get credit utilization percentage
     */
    public function getUtilizationPercentageAttribute(): float
    {
        if ($this->credit_amount <= 0) {
            return 0;
        }
        
        return min(100, ($this->used_credits / $this->credit_amount) * 100);
    }

    /**
     * Calculate cost per credit for this purchase
     */
    public function getCostPerCreditAttribute(): float
    {
        if ($this->credit_amount <= 0) {
            return 0;
        }
        
        return $this->price_paid / $this->credit_amount;
    }

    /**
     * Get days since purchase
     */
    public function getDaysSincePurchaseAttribute(): int
    {
        if (!$this->purchased_at) {
            return 0;
        }
        
        return $this->purchased_at->diffInDays(now());
    }

    /**
     * Calculate daily usage rate for this purchase
     */
    public function getDailyUsageRateAttribute(): float
    {
        $daysSince = $this->days_since_purchase;
        
        if ($daysSince <= 0) {
            return 0;
        }
        
        return $this->used_credits / $daysSince;
    }

    /**
     * Estimate days until credits are exhausted
     */
    public function getEstimatedDaysUntilExhaustionAttribute(): ?int
    {
        $dailyRate = $this->daily_usage_rate;
        
        if ($dailyRate <= 0 || $this->remaining_credits <= 0) {
            return null;
        }
        
        return (int) ceil($this->remaining_credits / $dailyRate);
    }

    /**
     * Get purchase efficiency score (0-100)
     * Based on cost per credit vs market average
     */
    public function getEfficiencyScoreAttribute(): float
    {
        $marketAverage = app(AICreditService::class)
            ->calculateMarketAverageCostPerCredit();
            
        if ($marketAverage <= 0) {
            return 100;
        }
        
        $efficiency = (1 - ($this->cost_per_credit / $marketAverage)) * 100;
        return max(0, min(100, $efficiency));
    }

    /**
     * Check if purchase is expired (older than 1 year)
     */
    public function isExpired(): bool
    {
        if (!$this->purchased_at) {
            return false;
        }
        
        return $this->purchased_at->addYear()->isPast();
    }

    /**
     * Get purchase performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'utilization_percentage' => $this->utilization_percentage,
            'cost_per_credit' => $this->cost_per_credit,
            'daily_usage_rate' => $this->daily_usage_rate,
            'days_since_purchase' => $this->days_since_purchase,
            'estimated_days_until_exhaustion' => $this->estimated_days_until_exhaustion,
            'efficiency_score' => $this->efficiency_score,
            'is_fully_consumed' => $this->isFullyConsumed(),
            'is_expired' => $this->isExpired(),
        ];
    }

    /**
     * Scope for purchases with remaining credits
     */
    public function scopeWithRemainingCredits($query)
    {
        return $query->whereRaw('credit_amount > (
            SELECT COALESCE(SUM(credit_used), 0) 
            FROM ai_credit_usages 
            WHERE purchase_id = ai_credit_purchases.id
        )');
    }

    /**
     * Scope for high-efficiency purchases
     */
    public function scopeHighEfficiency($query)
    {
        return $query->where('price_paid', '>', 0)
            ->whereRaw('(price_paid / credit_amount) < (
                SELECT AVG(price_paid / credit_amount) 
                FROM ai_credit_purchases 
                WHERE status = "completed" AND price_paid > 0
            )');
    }

    /**
     * Scope for recent purchases (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('purchased_at', '>=', now()->subDays(30));
    }
}