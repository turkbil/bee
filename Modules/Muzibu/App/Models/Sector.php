<?php

namespace Modules\Muzibu\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasUniversalSchemas;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Modules\Favorite\App\Traits\HasFavorites;
use Modules\Muzibu\App\Traits\HasPlaylistDistribution;
use Modules\Muzibu\App\Traits\HasCachedCounts;

class Sector extends BaseModel implements HasMedia
{
    use Sluggable, HasTranslations, HasUniversalSchemas, HasFactory, HasMediaManagement, SoftDeletes, Searchable, HasFavorites, HasPlaylistDistribution, HasCachedCounts;

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

    /**
     * Spatie Media Collections - hero tek dosya (yeni yüklenince eski silinir)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero')
            ->singleFile();
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
     * Sadece Spatie hero collection kullanır
     */
    public function getIconUrl(?int $width = 200, ?int $height = 200): ?string
    {
        $heroMedia = $this->getFirstMedia('hero');
        return $heroMedia ? thumb($heroMedia, $width, $height, ['scale' => 1]) : null;
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
            return url("/sectors/{$slug}");
        }

        return url("/{$locale}/sectors/{$slug}");
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

        // Cover image URL (hero collection)
        $heroMedia = $this->getFirstMedia('hero');
        $data['cover_url'] = $heroMedia ? thumb($heroMedia, 100, 100, ['scale' => 1]) : null;

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

    // ========================================
    // 🎤 Schema.org Implementation
    // ========================================

    /**
     * Get all schemas for this sector (DefinedTerm + Breadcrumb + FAQ + HowTo)
     *
     * @return array
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. DefinedTerm Schema (Primary)
        $definedTermSchema = $this->getDefinedTermSchema();
        if ($definedTermSchema) {
            $schemas['definedTerm'] = $definedTermSchema;
        }

        // 2. Breadcrumb Schema
        $breadcrumbSchema = $this->getBreadcrumbSchema();
        if ($breadcrumbSchema) {
            $schemas['breadcrumb'] = $breadcrumbSchema;
        }

        // 3. FAQ Schema (from HasUniversalSchemas trait)
        $faqSchema = $this->getFaqSchema();
        if ($faqSchema) {
            $schemas['faq'] = $faqSchema;
        }

        // 4. HowTo Schema (from HasUniversalSchemas trait)
        $howtoSchema = $this->getHowToSchema();
        if ($howtoSchema) {
            $schemas['howto'] = $howtoSchema;
        }

        return $schemas;
    }

    /**
     * Generate DefinedTerm Schema (for Sector taxonomy)
     *
     * @return array|null
     */
    protected function getDefinedTermSchema(): ?array
    {
        $locale = app()->getLocale();
        $name = $this->getTranslated('title', $locale);

        if (!$name) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'DefinedTerm',
            'name' => $name,
            'url' => $this->getUrl($locale),
            'inDefinedTermSet' => [
                '@type' => 'DefinedTermSet',
                'name' => 'Music Sectors'
            ]
        ];

        // Description
        $description = $this->getTranslated('description', $locale);
        if ($description) {
            $schema['description'] = strip_tags($description);
        }

        // Sector icon/image
        $heroMedia = $this->getFirstMedia('hero');
        if ($heroMedia) {
            $schema['image'] = thumb($heroMedia, 800, 800, ['quality' => 90]);
        }

        // Number of radios in this sector
        $radiosCount = $this->radios()->where('is_active', true)->count();
        if ($radiosCount > 0) {
            $schema['termCode'] = (string) $radiosCount . ' radios';
        }

        return $schema;
    }

    /**
     * Generate BreadcrumbList Schema
     *
     * Structure: Home → Sectors → Current Sector
     *
     * @return array|null
     */
    public function getBreadcrumbSchema(): ?array
    {
        $locale = app()->getLocale();
        $breadcrumbs = [];
        $position = 1;

        // 1. Home
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => __('Ana Sayfa'),
            'item' => url('/')
        ];

        // 2. Sectors Ana Sayfa
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Muzibu', 'sectors.index');
        $sectorsIndexUrl = $locale === get_tenant_default_locale()
            ? url("/{$moduleSlug}")
            : url("/{$locale}/{$moduleSlug}");

        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Sektörler',
            'item' => $sectorsIndexUrl
        ];

        // 3. Current Sector
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $this->getTranslated('title', $locale),
            'item' => $this->getUrl($locale)
        ];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        ];
    }

    // ========================================
    // 🎵 HasCachedCounts Implementation
    // ========================================

    /**
     * HasCachedCounts configuration
     * Defines cached count fields and their calculators
     *
     * Sector'ün şarkıları playlist'ler üzerinden gelir:
     * - songs_count: Tüm playlist'lerdeki benzersiz aktif şarkıların toplamı
     * - total_duration: Tüm playlist'lerdeki aktif şarkıların toplam süresi
     */
    protected function getCachedCountsConfig(): array
    {
        return [
            'songs_count' => function() {
                // Sector'e ait tüm playlist'lerdeki benzersiz şarkıları say
                $playlistIds = $this->playlists()->pluck('playlist_id')->toArray();

                if (empty($playlistIds)) {
                    return 0;
                }

                // Playlist'lerdeki benzersiz şarkı ID'lerini bul
                $uniqueSongIds = \DB::table('muzibu_playlist_song')
                    ->whereIn('playlist_id', $playlistIds)
                    ->join('muzibu_songs', 'muzibu_playlist_song.song_id', '=', 'muzibu_songs.song_id')
                    ->where('muzibu_songs.is_active', true)
                    ->distinct()
                    ->pluck('muzibu_songs.song_id');

                return $uniqueSongIds->count();
            },
            'total_duration' => function() {
                // Sector'e ait tüm playlist'lerdeki benzersiz şarkıların toplam süresini hesapla
                $playlistIds = $this->playlists()->pluck('playlist_id')->toArray();

                if (empty($playlistIds)) {
                    return 0;
                }

                // Playlist'lerdeki benzersiz şarkı ID'lerini bul ve sürelerini topla
                $totalDuration = \DB::table('muzibu_playlist_song')
                    ->whereIn('playlist_id', $playlistIds)
                    ->join('muzibu_songs', 'muzibu_playlist_song.song_id', '=', 'muzibu_songs.song_id')
                    ->where('muzibu_songs.is_active', true)
                    ->distinct()
                    ->sum('muzibu_songs.duration');

                return (int) $totalDuration;
            },
        ];
    }

    /**
     * Songs count accessor (cached)
     */
    public function getSongsCountAttribute(): int
    {
        return $this->getCachedCount('songs_count');
    }

    /**
     * Total duration accessor (cached)
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->getCachedCount('total_duration');
    }

    /**
     * Formatlanmış toplam süre (HH:MM:SS veya MM:SS)
     */
    public function getFormattedTotalDuration(): string
    {
        $totalSeconds = $this->total_duration;

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
