<?php

namespace Modules\Muzibu\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Modules\Favorite\App\Traits\HasFavorites;
use Modules\Muzibu\App\Traits\HasPlaylistDistribution;

class Sector extends BaseModel implements HasMedia
{
    use Sluggable, HasTranslations, HasFactory, HasMediaManagement, SoftDeletes, Searchable, HasFavorites, HasPlaylistDistribution;

    protected $table = 'muzibu_sectors';
    protected $primaryKey = 'sector_id';
    /**
     * Dinamik connection resolver
     * Central tenant ise mysql (default), değilse tenant connection
     */
    public function getConnectionName()
    {
        // ✅ Muzibu modülü tenant-specific, ZORLA tenant connection!
        // Tenant 1001 (muzibu) için ayrı database var
        if (false) {
            return 'tenant';
        }
        return 'tenant';
    }


    protected $fillable = [
        'title',
        'slug',
        'description',
        'media_id',
        'is_active',
    ];

    protected $casts = [
        // NOT: title, slug, description CAST'LANMAMALI!
        // HasTranslations trait bunları otomatik yönetiyor
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'description'];

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

    // playlists() metodu artık HasPlaylistDistribution trait'inden geliyor
    // Eski tablo: muzibu_playlist_sector → Yeni tablo: muzibu_playlistables

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
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'media_id');
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
     * Alias for getIconUrl - Search compatibility
     */
    public function getCoverUrl(?int $width = 200, ?int $height = 200): ?string
    {
        return $this->getIconUrl($width, $height);
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

    /**
     * Meilisearch - Get searchable data
     */
    public function toSearchableArray(): array
    {
        try {
            $connection = (tenant() && !tenant()->central) ? 'tenant' : 'mysql';
            $langCodes = \DB::connection($connection)->table('tenant_languages')->where('is_active', 1)->pluck('code')->toArray();
        } catch (\Exception $e) {
            $langCodes = ['tr', 'en'];
        }

        $data = [
            'id' => $this->sector_id,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->timestamp,
        ];

        foreach ($langCodes as $langCode) {
            $data["title_{$langCode}"] = $this->getTranslated('title', $langCode);
            $data["description_{$langCode}"] = $this->getTranslated('description', $langCode);
        }

        return $data;
    }

    public function searchableAs(): string
    {
        $tenantId = tenant() ? tenant()->id : 1001;
        return "tenant_{$tenantId}_sectors";
    }

    public function getScoutKey()
    {
        return $this->sector_id;
    }

    public function getScoutKeyName()
    {
        return 'sector_id';
    }
}
