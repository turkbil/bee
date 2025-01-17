<?php

namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Scopes\TenantScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioCategory extends BaseModel
{
    use Sluggable, SoftDeletes;

    protected $primaryKey = 'portfolio_category_id';

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'order', // Sıralama
        'keywords', // Anahtar kelimeler
        'description', // Açıklama
        'is_active',
    ];

    /**
     * Sluggable Ayarları
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title', // Başlık üzerinden slug oluşturulur
                'unique' => true,    // Benzersiz olmasını sağlar
                'onUpdate' => false, // Güncellemede slug değiştirilmez
            ],
        ];
    }

    /**
     * Portfolyolar ilişkisi
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'portfolio_category_id', 'portfolio_category_id');
    }

    /**
     * Dinamik tenant ID sütunu tanımı
     */
    public function getTenantIdColumn()
    {
        return 'tenant_id';
    }

    /**
     * Global Scope ekleme
     */
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope());
    }
}