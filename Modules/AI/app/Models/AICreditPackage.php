<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AICreditPackage extends Model
{
    use HasFactory;
    
    protected $table = 'ai_credit_packages';
    
    protected $fillable = [
        'name',
        'description', 
        'credits',
        'price_usd',
        'price_try',
        'is_popular',
        'is_active',
        'sort_order',
        'features',
        'discount_percentage'
    ];
    
    protected $casts = [
        'credits' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'price_try' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'features' => 'array',
        'discount_percentage' => 'decimal:2'
    ];
    
    /**
     * Aktif paketleri getir
     */
    public static function getActivePackages()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price_usd')
            ->get();
    }
    
    /**
     * Popüler paketi getir
     */
    public static function getPopularPackage()
    {
        return self::where('is_active', true)
            ->where('is_popular', true)
            ->first();
    }
    
    /**
     * Kredi/USD oranını hesapla
     */
    public function getCreditPerDollarAttribute()
    {
        return $this->price_usd > 0 ? round($this->credits / $this->price_usd, 2) : 0;
    }
    
    /**
     * İndirimli fiyat hesapla
     */
    public function getDiscountedPriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return round($this->price_usd * (1 - $this->discount_percentage / 100), 2);
        }
        return $this->price_usd;
    }
    
    /**
     * TL fiyat varsa onu döndür, yoksa USD*TL kurunu hesapla
     */
    public function getPriceInTry($usdToTryRate = 34.0)
    {
        if ($this->price_try) {
            return $this->price_try;
        }
        
        return round($this->discounted_price * $usdToTryRate, 2);
    }
}
