<?php

namespace Modules\Coupon\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends BaseModel
{
    use HasTranslations, HasFactory, SoftDeletes;

    protected $primaryKey = 'coupon_id';

    protected $fillable = [
        'title',
        'code',
        'description',
        'coupon_type',
        'discount_percentage',
        'discount_amount',
        'max_discount_amount',
        'buy_quantity',
        'get_quantity',
        'applicable_product_ids',
        'usage_limit_total',
        'usage_limit_per_customer',
        'used_count',
        'minimum_order_amount',
        'maximum_order_amount',
        'minimum_items',
        'applies_to',
        'category_ids',
        'product_ids',
        'brand_ids',
        'excluded_category_ids',
        'excluded_product_ids',
        'customer_eligibility',
        'customer_group_ids',
        'customer_ids',
        'valid_from',
        'valid_until',
        'can_combine_with_other_coupons',
        'can_combine_with_sales',
        'is_active',
        'is_public',
        'banner_text',
        'banner_color',
        'terms',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'applicable_product_ids' => 'array',
        'category_ids' => 'array',
        'product_ids' => 'array',
        'brand_ids' => 'array',
        'excluded_category_ids' => 'array',
        'excluded_product_ids' => 'array',
        'customer_group_ids' => 'array',
        'customer_ids' => 'array',
        'banner_text' => 'array',
        'metadata' => 'array',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_order_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'can_combine_with_other_coupons' => 'boolean',
        'can_combine_with_sales' => 'boolean',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    protected $translatable = ['title', 'description', 'banner_text'];

    // Relationships
    public function usages()
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id', 'coupon_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit_total')
                  ->orWhereRaw('used_count < usage_limit_total');
            });
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('valid_until', '<=', now())
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('usage_limit_total')
                     ->whereRaw('used_count >= usage_limit_total');
              });
        });
    }

    // Validation Methods
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->usage_limit_total && $this->used_count >= $this->usage_limit_total) {
            return false;
        }

        return true;
    }

    public function isUsableBy(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $userUsageCount = $this->usages()
            ->where('user_id', $user->id)
            ->count();

        return $userUsageCount < $this->usage_limit_per_customer;
    }

    public function canApplyTo(float $amount): bool
    {
        if ($this->minimum_order_amount && $amount < $this->minimum_order_amount) {
            return false;
        }

        if ($this->maximum_order_amount && $amount > $this->maximum_order_amount) {
            return false;
        }

        return true;
    }

    // Calculation Methods
    public function apply(float $amount): float
    {
        $discount = 0;

        if ($this->coupon_type === 'percentage') {
            $discount = $amount * ($this->discount_percentage / 100);
        } elseif ($this->coupon_type === 'fixed_amount') {
            $discount = $this->discount_amount;
        }

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $amount);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    // Accessors
    public function getTitleTextAttribute()
    {
        return $this->getTranslated('title', app()->getLocale()) ?? '';
    }

    public function getDescriptionTextAttribute()
    {
        return $this->getTranslated('description', app()->getLocale()) ?? '';
    }

    public function getDiscountDisplayAttribute(): string
    {
        if ($this->coupon_type === 'percentage') {
            return '%' . intval($this->discount_percentage);
        }

        if ($this->coupon_type === 'fixed_amount') {
            return number_format($this->discount_amount, 2) . ' ₺';
        }

        if ($this->coupon_type === 'free_shipping') {
            return 'Ücretsiz Kargo';
        }

        if ($this->coupon_type === 'buy_x_get_y') {
            return $this->buy_quantity . ' Al ' . $this->get_quantity . ' Öde';
        }

        return '-';
    }

    public function getUsageDisplayAttribute(): string
    {
        if ($this->usage_limit_total) {
            return $this->used_count . ' / ' . $this->usage_limit_total;
        }

        return $this->used_count . ' / ∞';
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return 'expired';
        }

        if ($this->usage_limit_total && $this->used_count >= $this->usage_limit_total) {
            return 'limit_reached';
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return 'scheduled';
        }

        return 'active';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'expired' => 'danger',
            'limit_reached' => 'warning',
            'scheduled' => 'info',
            default => 'secondary',
        };
    }
}
