<?php
namespace Modules\Announcement\App\Models;

use App\Models\BaseModel;
use Cviebrock\EloquentSluggable\Sluggable;

class Announcement extends BaseModel
{
    use Sluggable;

    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
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
    
}