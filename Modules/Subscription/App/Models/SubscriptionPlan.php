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
        'currency_id',
        'tax_rate',
        'price_display_mode',
        'trial_days',
        'device_limit',
        'custom_limits',
        'enabled_features',
        'sort_order',
        'is_trial',
        'is_featured',
        'is_active',
        'is_public',
        'subscribers_count',
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
        'tax_rate' => 'decimal:2',
        'is_trial' => 'boolean',
        'is_featured' => 'boolean',
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
        return $this->hasMany(Subscription::class, 'subscription_plan_id', 'subscription_plan_id');
    }

    public function currency()
    {
        return $this->belongsTo(\Modules\Shop\App\Models\ShopCurrency::class, 'currency_id', 'currency_id');
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

    /**
     * Cycle'ın fiyat türünü al (with_tax / without_tax)
     * Default: without_tax (Shop pattern ile uyumlu - Muzibu mevcut fiyatlar KDV hariç)
     */
    public function getCyclePriceType(string $cycleKey): string
    {
        $cycle = $this->getCycle($cycleKey);

        // Cycle'da price_type belirtilmişse kullan
        if (isset($cycle['price_type'])) {
            return $cycle['price_type'];
        }

        // Global default: without_tax (Muzibu mevcut fiyatlar KDV hariç)
        return 'without_tax';
    }

    /**
     * Cycle'ın KDV HARİÇ fiyatını al (Base Price - Cart için)
     * price_type'a göre otomatik hesaplar
     * - without_tax ise: direkt price döner
     * - with_tax ise: KDV ayrıştırılır
     */
    public function getCycleBasePrice(string $cycleKey): ?float
    {
        $cycle = $this->getCycle($cycleKey);
        if (!$cycle || !isset($cycle['price'])) {
            return null;
        }

        $price = (float) $cycle['price'];
        $priceType = $this->getCyclePriceType($cycleKey);
        $taxRate = $this->tax_rate ?? 20.0;

        // Eğer fiyat KDV dahil girilmişse, KDV'yi ayrıştır
        if ($priceType === 'with_tax') {
            return $price / (1 + $taxRate / 100);
        }

        // Fiyat zaten KDV hariç (Muzibu default durum)
        return $price;
    }

    /**
     * Belirli bir cycle'ın KDV dahil fiyatını hesapla (Runtime calculation)
     * price_type'a göre otomatik hesaplar
     * - without_tax ise: price * (1 + tax_rate)
     * - with_tax ise: direkt price döner
     */
    public function getCyclePriceWithTax(string $cycleKey): ?float
    {
        $cycle = $this->getCycle($cycleKey);
        if (!$cycle || !isset($cycle['price'])) {
            return null;
        }

        $price = (float) $cycle['price'];
        $priceType = $this->getCyclePriceType($cycleKey);
        $taxRate = $this->tax_rate ?? 20.0;

        // Eğer fiyat KDV dahil girilmişse, direkt döndür
        if ($priceType === 'with_tax') {
            return $price;
        }

        // Fiyat KDV hariç girilmiş, KDV ekle (Muzibu default durum)
        return $price * (1 + $taxRate / 100);
    }

    /**
     * Belirli bir cycle'ın KDV tutarını hesapla
     */
    public function getCycleTaxAmount(string $cycleKey): ?float
    {
        $basePrice = $this->getCycleBasePrice($cycleKey);
        $priceWithTax = $this->getCyclePriceWithTax($cycleKey);

        if (!$basePrice || !$priceWithTax) {
            return null;
        }

        return $priceWithTax - $basePrice;
    }
}
