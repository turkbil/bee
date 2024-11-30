<?php
namespace Modules\Page\App\Models;

use App\Models\BaseModel;
use Cviebrock\EloquentSluggable\Sluggable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Page extends BaseModel
{
    use Sluggable, BelongsToTenant;

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
     * Sluggable Paket Ayarları
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'   => 'title',
                'onUpdate' => true,
            ],
        ];
    }
}
