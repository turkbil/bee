<?php
namespace Modules\Announcement\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;

class Announcement extends BaseModel
{
    use Sluggable, HasTranslations;

    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'metakey',
        'metadesc',
        'is_active',
    ];

    protected $casts = [
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
     * Sluggable Ayarları - JSON slug alanları için devre dışı
     */
    public function sluggable(): array
    {
        return [
            // JSON slug alanları manuel olarak yönetiliyor
        ];
    }
    
}