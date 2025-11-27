<?php

namespace Modules\Subscription\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends BaseModel
{
    use HasTranslations, HasFactory, SoftDeletes;

    protected $primaryKey = 'subscription_plan_id';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'features',
        'price_daily',
        'price_weekly',
        'price_monthly',
        'price_quarterly',
        'price_yearly',
        'compare_price_monthly',
        'compare_price_yearly',
        'currency',
        'has_trial',
        'trial_days',
        'device_limit',
        'requires_payment_method',
        'max_products',
        'max_orders',
        'max_storage_mb',
        'custom_limits',
        'has_analytics',
        'has_priority_support',
        'has_api_access',
        'enabled_features',
        'default_billing_cycle',
        'sort_order',
        'is_featured',
        'is_popular',
        'badge_text',
        'highlight_color',
        'is_active',
        'is_public',
        'subscribers_count',
        'terms',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'features' => 'array',
        'custom_limits' => 'array',
        'enabled_features' => 'array',
        'metadata' => 'array',
        'price_daily' => 'decimal:2',
        'price_weekly' => 'decimal:2',
        'price_monthly' => 'decimal:2',
        'price_quarterly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'compare_price_monthly' => 'decimal:2',
        'compare_price_yearly' => 'decimal:2',
        'has_trial' => 'boolean',
        'requires_payment_method' => 'boolean',
        'has_analytics' => 'boolean',
        'has_priority_support' => 'boolean',
        'has_api_access' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    protected $translatable = ['title', 'description'];

    /**
     * Sluggable devre dışı - slug manuel yönetiliyor
     */
    public function sluggable(): array
    {
        return [];
    }

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id', 'subscription_plan_id');
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
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

    public function getPriceAttribute()
    {
        return $this->price_monthly;
    }

    public function getPriceByBillingCycle(string $cycle): float
    {
        return match($cycle) {
            'daily' => (float) $this->price_daily,
            'weekly' => (float) $this->price_weekly,
            'monthly' => (float) $this->price_monthly,
            'quarterly' => (float) $this->price_quarterly,
            'yearly' => (float) $this->price_yearly,
            default => (float) $this->price_monthly,
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_monthly, 2) . ' ' . ($this->currency ?? '₺');
    }

    public function getActiveSubscribersCountAttribute(): int
    {
        return $this->subscriptions()->where('status', 'active')->count();
    }

    public function getYearlySavings(): float
    {
        $monthlyTotal = $this->price_monthly * 12;
        return $monthlyTotal - $this->price_yearly;
    }

    public function getYearlySavingsPercent(): int
    {
        $monthlyTotal = $this->price_monthly * 12;
        if ($monthlyTotal <= 0) return 0;
        return (int) round((($monthlyTotal - $this->price_yearly) / $monthlyTotal) * 100);
    }
}
