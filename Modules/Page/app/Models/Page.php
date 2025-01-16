<?php
namespace Modules\Page\App\Models;

use App\Models\BaseModel;
use App\Scopes\TenantScope;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends BaseModel
{
    use Sluggable;

    protected $primaryKey = 'page_id';

    protected $fillable = [
        'tenant_id',
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

    // Dinamik tenant ID sütunu tanımı
    public function getTenantIdColumn()
    {
        return 'tenant_id';
    }

    // Global Scope ekleme
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope());
    }
}
