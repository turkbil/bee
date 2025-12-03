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
        'billing_cycles', // Dinamik cycles (15 gün, 1 ay, 2 ay...)
        'currency',
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
        'billing_cycles' => 'array', // Dinamik cycles
        'custom_limits' => 'array',
        'enabled_features' => 'array',
        'metadata' => 'array',
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

    /**
     * Cycles accessor - billing_cycles'ı cycles olarak döndür
     */
    public function getCyclesAttribute()
    {
        return $this->billing_cycles;
    }

    /**
     * Belirli bir cycle'ın fiyatını al
     */
    public function getCyclePrice(string $cycleKey): ?float
    {
        $cycles = $this->billing_cycles ?? [];
        return isset($cycles[$cycleKey]['price']) ? (float) $cycles[$cycleKey]['price'] : null;
    }

    /**
     * Belirli bir cycle'ın tüm bilgilerini al
     */
    public function getCycle(string $cycleKey): ?array
    {
        $cycles = $this->billing_cycles ?? [];
        return $cycles[$cycleKey] ?? null;
    }

    /**
     * Tüm cycles'ları sort_order'a göre sıralı al
     */
    public function getSortedCycles(): array
    {
        $cycles = $this->billing_cycles ?? [];

        uasort($cycles, function($a, $b) {
            return ($a['sort_order'] ?? 999) <=> ($b['sort_order'] ?? 999);
        });

        return $cycles;
    }

    /**
     * En düşük fiyatlı cycle'ı bul
     */
    public function getLowestPriceCycle(): ?array
    {
        $cycles = $this->billing_cycles ?? [];
        if (empty($cycles)) return null;

        return collect($cycles)->sortBy('price')->first();
    }

    public function getActiveSubscribersCountAttribute(): int
    {
        return $this->subscriptions()->where('status', 'active')->count();
    }
}
