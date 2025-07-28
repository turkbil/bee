<?php
namespace Modules\ThemeManagement\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Cviebrock\EloquentSluggable\Sluggable;

class Theme extends BaseModel implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, CentralConnection, Sluggable;

    protected $primaryKey = 'theme_id';

    protected $fillable = [
        'name',
        'title',
        'slug',
        'folder_name',
        'description',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
             ->singleFile()
             ->useDisk('public');
    }
}