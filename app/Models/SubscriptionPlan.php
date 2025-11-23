<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use SoftDeletes;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'duration_days',
        'duration_type', // monthly, yearly
        'features',
        'is_active',
        'is_featured',
        'sort_order',
        'trial_days',
        'max_devices',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'json',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Get all subscriptions for this plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Scope: Active plans only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Featured plans
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ₺';
    }

    /**
     * Get duration label
     */
    public function getDurationLabelAttribute(): string
    {
        return match($this->duration_type) {
            'monthly' => 'Aylık',
            'yearly' => 'Yıllık',
            default => $this->duration_days . ' gün'
        };
    }
}
