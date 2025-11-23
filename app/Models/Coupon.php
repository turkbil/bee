<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use SoftDeletes;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'title',
        'description',
        'type', // percentage, fixed
        'value',
        'min_amount',
        'max_discount',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_to', // all, subscription, product
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get coupon usages
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Scope: Valid coupons
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                  ->orWhereColumn('usage_count', '<', 'usage_limit');
            });
    }

    /**
     * Scope: Expired coupons
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '<=', now())
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('usage_limit')
                     ->whereColumn('usage_count', '>=', 'usage_limit');
              });
        });
    }

    /**
     * Check if coupon is valid for user
     */
    public function isValidFor(User $user): bool
    {
        // Check if active
        if (!$this->is_active) return false;

        // Check date range
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;

        // Check per user limit
        if ($this->per_user_limit) {
            $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $this->per_user_limit) return false;
        }

        return true;
    }

    /**
     * Calculate discount for amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->min_amount && $amount < $this->min_amount) {
            return 0;
        }

        $discount = match($this->type) {
            'percentage' => $amount * ($this->value / 100),
            'fixed' => $this->value,
            default => 0
        };

        // Apply max discount cap
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return min($discount, $amount);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
