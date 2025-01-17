<?php

namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Scopes\TenantScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Portfolio extends BaseModel implements HasMedia
{
    use Sluggable, SoftDeletes, InteractsWithMedia;

    protected $primaryKey = 'portfolio_id';

    protected $fillable = [
        'tenant_id',
        'portfolio_category_id', // Kategori ID
        'title',
        'slug',
        'body',
        'css',
        'js',
        'metakey',
        'metadesc',
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
     * Kategori ilişkisi
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id', 'portfolio_category_id');
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
