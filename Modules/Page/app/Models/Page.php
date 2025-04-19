<?php
namespace Modules\Page\App\Models;

use App\Models\BaseModel;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\HasContentViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;

class Page extends BaseModel implements Viewable
{
    use Sluggable, HasContentViews;

    protected $primaryKey = 'page_id';
    
    protected $appends = ['views_count'];

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
     * Görüntülenme sayısını döndürür
     *
     * @return int
     */
    public function getViewsCountAttribute(): int
    {
        return $this->views()->count();
    }
}