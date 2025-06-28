<?php
namespace Modules\Page\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends BaseModel
{
    use Sluggable, HasTranslations;

    protected $primaryKey = 'page_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'css',
        'js',
        'metakey',
        'metadesc',
        'is_active',
        'is_homepage',
    ];

    protected $casts = [
        'is_homepage' => 'boolean',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
        'metakey' => 'array',
        'metadesc' => 'array',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body', 'metakey', 'metadesc'];

    /**
     * Sluggable Ayarları - JSON çoklu dil desteği için devre dışı
     * Artık HasTranslations trait'inde generateSlugForLocale() kullanılacak
     */
    public function sluggable(): array
    {
        return [
            // JSON column çalışmadığı için devre dışı
            // 'slug' => [
            //     'source' => 'title',
            //     'unique' => true,
            //     'onUpdate' => false,
            // ],
        ];
    }

    /**
     * Aktif sayfaları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Ana sayfayı getir
     */
    public function scopeHomepage($query)
    {
        return $query->where('is_homepage', true);
    }
    
}