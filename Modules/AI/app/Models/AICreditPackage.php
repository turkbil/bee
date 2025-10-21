<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AICreditPackage extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql'; // Central DB for all tenants
    protected $table = 'ai_credit_packages';
    
    protected $fillable = [
        'name',
        'description', 
        'credit_amount',
        'price',
        'currency',
        'is_popular',
        'is_active',
        'sort_order',
        'features',
        'discount_percentage'
    ];
    
    protected $casts = [
        'credit_amount' => 'decimal:2',
        'price' => 'decimal:2',
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
            ->orderBy('price')
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
     * Kredi/Fiyat oranını hesapla
     */
    public function getCreditPerPriceAttribute()
    {
        return $this->price > 0 ? round($this->credit_amount / $this->price, 2) : 0;
    }
    
    /**
     * İndirimli fiyat hesapla
     */
    public function getDiscountedPriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return round($this->price * (1 - $this->discount_percentage / 100), 2);
        }
        return $this->price;
    }
    
    /**
     * Paranın türüne göre formatlanmış fiyat
     */
    public function getFormattedPriceAttribute()
    {
        $price = $this->discounted_price;
        
        if ($this->currency === 'TRY') {
            return number_format($price, 2) . ' ₺';
        } elseif ($this->currency === 'USD') {
            return '$' . number_format($price, 2);
        } elseif ($this->currency === 'EUR') {
            return '€' . number_format($price, 2);
        }
        
        return number_format($price, 2) . ' ' . $this->currency;
    }
}
