<?php

namespace Modules\Muzibu\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;

class Sector extends BaseModel implements HasMedia
{
    use Sluggable, HasTranslations, HasFactory, HasMediaManagement, SoftDeletes;

    protected $table = 'muzibu_sectors';
    protected $primaryKey = 'sector_id';

    protected $fillable = [
        'title',
        'slug',
        'media_id',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug'];

    /**
     * ID accessor - sector_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->sector_id;
    }

    /**
     * Sluggable Ayarları
     */
    public function sluggable(): array
    {
        return [];
    }

    /**
     * Aktif kayıtları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Playlist'ler ilişkisi (many-to-many)
     */
    public function playlists()
    {
        return $this->belongsToMany(
            Playlist::class,
            'muzibu_playlist_sector',
            'sector_id',
            'playlist_id',
            'sector_id',
            'playlist_id'
        );
    }

    /**
     * Radyo ilişkisi (many-to-many)
     */
    public function radios()
    {
        return $this->belongsToMany(
            Radio::class,
            'muzibu_radio_sector',
            'sector_id',
            'radio_id',
            'sector_id',
            'radio_id'
        );
    }

    /**
     * Thumbmaker media ilişkisi (Sector icon)
     * Not: Spatie'nin media() methodu ile çakışmamak için iconMedia() kullanıyoruz
     */
    public function iconMedia()
    {
        return $this->belongsTo(\Modules\MediaManagement\App\Models\Media::class, 'media_id');
    }

    /**
     * Sector icon URL'i (Thumbmaker helper ile)
     */
    public function getIconUrl(?int $width = 200, ?int $height = 200): ?string
    {
        if (!$this->media_id) {
            return null;
        }
        return thumb($this->iconMedia, $width, $height);
    }

    /**
     * Media collections config
     */
    protected function getMediaConfig(): array
    {
        return [
            'icon' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => 1,
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => ['thumb', 'medium'],
                'sortable' => false,
            ],
        ];
    }

    /**
     * Sector için locale-aware URL
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            return url("/muzibu/sector/{$slug}");
        }

        return url("/{$locale}/muzibu/sector/{$slug}");
    }
}
