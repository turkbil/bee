<?php

namespace Modules\Muzibu\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use App\Traits\HasUniversalSchemas;
use App\Contracts\TranslatableEntity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Modules\Favorite\App\Traits\HasFavorites;
use Modules\ReviewSystem\App\Traits\HasReviews;
use Modules\Muzibu\App\Traits\HasCachedCounts;

class Album extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasUniversalSchemas, HasFactory, HasMediaManagement, SoftDeletes, HasFavorites, HasReviews, Searchable, HasCachedCounts;

    protected $table = 'muzibu_albums';
    protected $primaryKey = 'album_id';

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
        'artist_id',
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
     * ID accessor - album_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->album_id;
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

    /**
     * Sanatçı ilişkisi
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'artist_id');
    }

    /**
     * Şarkılar ilişkisi
     */
    public function songs()
    {
        return $this->hasMany(Song::class, 'album_id', 'album_id');
    }

    /**
     * HasCachedCounts configuration
     * Defines cached count fields and their calculators
     */
    protected function getCachedCountsConfig(): array
    {
        return [
            'songs_count' => fn() => $this->songs()->where('is_active', true)->count(),
            'total_duration' => fn() => $this->songs()->where('is_active', true)->sum('duration'),
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
     * Toplam süreyi hesapla (cached)
     */
    public function getTotalDuration(): int
    {
        return $this->getCachedCount('total_duration');
    }

    /**
     * Formatlanmış toplam süre (HH:MM:SS veya MM:SS)
     */
    public function getFormattedTotalDuration(): string
    {
        $totalSeconds = $this->getTotalDuration();

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Thumbmaker media ilişkisi (Album cover)
     * Not: Spatie'nin media() methodu ile çakışmamak için coverMedia() kullanıyoruz
     */
    public function coverMedia()
    {
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'media_id');
    }

    /**
     * Album cover URL'i (Thumbmaker helper ile)
     * Sadece Spatie hero collection kullanır
     */
    public function getCoverUrl(?int $width = 800, ?int $height = 800, int $quality = 90): ?string
    {
        $heroMedia = $this->getFirstMedia('hero');
        return $heroMedia ? thumb($heroMedia, $width, $height, ['quality' => $quality, 'scale' => 1]) : null;
    }

    /**
     * Cover URL accessor (kare, thumbmaker ile)
     * Frontend için otomatik square cover (200x200, ortadan kırp)
     */
    public function getCoverUrlAttribute(): string
    {
        $heroMedia = $this->getFirstMedia('hero');
        return $heroMedia ? thumb($heroMedia, 200, 200, ['scale' => 1]) : '';
    }

    /**
     * Player blur background (album kapağından blur arka plan)
     * Orta kısımdan ince şerit alıp çok blur yapar
     */
    public function getBlurBackgroundAttribute(): ?string
    {
        $heroMedia = $this->getFirstMedia('hero');
        return $heroMedia ? thumb($heroMedia, 1200, 200, ['fit' => 'crop', 'blur' => 80]) : null;
    }

    /**
     * HasSeo trait fallback implementations
     */
    public function getSeoFallbackTitle(): ?string
    {
        return $this->getTranslated('title', app()->getLocale());
    }

    public function getSeoFallbackDescription(): ?string
    {
        $description = $this->getTranslated('description', app()->getLocale());
        return $description ? \Illuminate\Support\Str::limit(strip_tags($description), 160) : null;
    }

    public function getSeoFallbackKeywords(): array
    {
        $keywords = [];

        // Album title
        $title = $this->getSeoFallbackTitle();
        if ($title) {
            $words = array_filter(explode(' ', strtolower($title)), fn($word) => strlen($word) > 3);
            $keywords = array_merge($keywords, array_slice($words, 0, 3));
        }

        // Artist name
        if ($this->artist) {
            $artistName = $this->artist->getTranslated('title', app()->getLocale());
            if ($artistName) {
                $keywords[] = strtolower($artistName);
            }
        }

        return array_slice(array_unique($keywords), 0, 5);
    }

    public function getSeoFallbackCanonicalUrl(): ?string
    {
        // Use existing getUrl() method for consistency
        return $this->getUrl();
    }

    public function getSeoFallbackImage(): ?string
    {
        return $this->getFirstMediaUrl('hero') ?? null;
    }

    public function getSeoFallbackSchemaMarkup(): ?array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MusicAlbum',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'image' => $this->getSeoFallbackImage(),
        ];

        // Add artist (byArtist)
        if ($this->artist) {
            $schema['byArtist'] = [
                '@type' => 'MusicGroup',
                'name' => $this->artist->getTranslated('title', app()->getLocale()),
                'url' => $this->artist->getSeoFallbackCanonicalUrl(),
            ];
        }

        // Add tracks count
        $songsCount = $this->songs()->count();
        if ($songsCount > 0) {
            $schema['numTracks'] = $songsCount;
        }

        // ⭐ Aggregated Rating - HasReviews trait'inden alınır
        if (method_exists($this, 'averageRating') && method_exists($this, 'ratingsCount')) {
            $avgRating = $this->averageRating();
            $ratingCount = $this->ratingsCount();

            if ($avgRating > 0 && $ratingCount > 0) {
                $schema['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => (string) number_format($avgRating, 1),
                    'reviewCount' => $ratingCount,
                    'bestRating' => '5',
                    'worstRating' => '1',
                ];
            }
        }

        return $schema;
    }

    /**
     * TranslatableEntity interface implementation
     */
    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
            'description' => 'html',
            'slug' => 'auto',
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info("Album çevirisi tamamlandı", [
            'album_id' => $this->album_id,
            'artist_id' => $this->artist_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'album_id';
    }

    /**
     * Media collections config
     */
    protected function getMediaConfig(): array
    {
        return [
            'hero' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => 1,
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => ['thumb', 'medium', 'large'],
                'sortable' => false,
            ],
        ];
    }

    /**
     * Album için locale-aware URL
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            return url("/albums/{$slug}");
        }

        return url("/{$locale}/albums/{$slug}");
    }

    /**
     * Get the indexable data array for the model (Meilisearch)
     */
    public function toSearchableArray(): array
    {
        try {
            $connection = (tenant() && !tenant()->central) ? 'tenant' : 'mysql';
            $langCodes = \DB::connection($connection)
                ->table('tenant_languages')
                ->where('is_active', 1)
                ->pluck('code')
                ->toArray();
        } catch (\Exception $e) {
            $langCodes = ['tr', 'en'];
        }

        $data = [
            'id' => $this->album_id,
            'release_date' => $this->release_date,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->timestamp,
        ];

        foreach ($langCodes as $langCode) {
            $data["title_{$langCode}"] = $this->getTranslated('title', $langCode);
            $data["description_{$langCode}"] = $this->getTranslated('description', $langCode);
        }

        if ($this->artist) {
            foreach ($langCodes as $langCode) {
                $data["artist_title_{$langCode}"] = $this->artist->getTranslated('title', $langCode);
            }
        }

        // Cover image URL (hero collection)
        $heroMedia = $this->getFirstMedia('hero');
        $data['cover_url'] = $heroMedia ? thumb($heroMedia, 100, 100, ['scale' => 1]) : null;

        return $data;
    }

    public function searchableAs(): string
    {
        $tenantId = tenant() ? tenant()->id : 1001;
        return "tenant_{$tenantId}_albums";
    }

    public function getScoutKey()
    {
        return $this->album_id;
    }

    public function getScoutKeyName()
    {
        return 'album_id';
    }

    // ========================================
    // 💿 Schema.org Implementation
    // ========================================

    /**
     * Get all schemas for this album (MusicAlbum + Breadcrumb + FAQ + HowTo)
     *
     * @return array
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. MusicAlbum Schema (Primary)
        $musicAlbumSchema = $this->getMusicAlbumSchema();
        if ($musicAlbumSchema) {
            $schemas['musicAlbum'] = $musicAlbumSchema;
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
     * Generate MusicAlbum Schema
     *
     * @return array|null
     */
    protected function getMusicAlbumSchema(): ?array
    {
        $locale = app()->getLocale();
        $title = $this->getTranslated('title', $locale);

        if (!$title) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MusicAlbum',
            'name' => $title,
            'url' => $this->getUrl($locale),
        ];

        // Description
        $description = $this->getTranslated('description', $locale);
        if ($description) {
            $schema['description'] = strip_tags($description);
        }

        // Cover image
        $heroMedia = $this->getFirstMedia('hero');
        if ($heroMedia) {
            $schema['image'] = thumb($heroMedia, 1200, 1200, ['quality' => 90]);
        }

        // Artist (byArtist)
        if ($this->artist) {
            $artistName = $this->artist->getTranslated('name', $locale);
            if ($artistName) {
                $schema['byArtist'] = [
                    '@type' => 'MusicGroup',
                    'name' => $artistName,
                    'url' => $this->artist->getUrl($locale)
                ];
            }
        }

        // Number of tracks
        $songsCount = $this->getSongsCountAttribute();
        if ($songsCount > 0) {
            $schema['numTracks'] = $songsCount;

            // Track list (first 10 songs for schema)
            $songs = $this->songs()->where('is_active', true)->limit(10)->get();
            if ($songs->count() > 0) {
                $trackList = [];
                foreach ($songs as $index => $song) {
                    $songTitle = $song->getTranslated('title', $locale);
                    if ($songTitle) {
                        $trackList[] = [
                            '@type' => 'MusicRecording',
                            'position' => $index + 1,
                            'name' => $songTitle,
                            'url' => $song->getUrl($locale),
                            'duration' => $song->duration ? sprintf('PT%dM%dS', floor($song->duration / 60), $song->duration % 60) : null
                        ];
                    }
                }
                if (!empty($trackList)) {
                    $schema['track'] = $trackList;
                }
            }
        }

        // Total duration
        $totalDuration = $this->getTotalDuration();
        if ($totalDuration > 0) {
            $hours = floor($totalDuration / 3600);
            $minutes = floor(($totalDuration % 3600) / 60);
            $seconds = $totalDuration % 60;

            if ($hours > 0) {
                $schema['duration'] = sprintf('PT%dH%dM%dS', $hours, $minutes, $seconds);
            } else {
                $schema['duration'] = sprintf('PT%dM%dS', $minutes, $seconds);
            }
        }

        // Aggregate rating (from HasReviews trait)
        if (method_exists($this, 'reviews')) {
            $reviewCount = $this->reviews()->count();
            if ($reviewCount > 0) {
                $avgRating = $this->reviews()->avg('rating');
                $schema['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => round($avgRating, 1),
                    'reviewCount' => $reviewCount,
                    'bestRating' => 5,
                    'worstRating' => 1
                ];
            }
        }

        return $schema;
    }

    /**
     * Generate BreadcrumbList Schema
     *
     * Structure: Home → Albums → Artist (if exists) → Current Album
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

        // 2. Albums Ana Sayfa
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Muzibu', 'albums.index');
        $albumsIndexUrl = $locale === get_tenant_default_locale()
            ? url("/{$moduleSlug}")
            : url("/{$locale}/{$moduleSlug}");

        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Albümler',
            'item' => $albumsIndexUrl
        ];

        // 3. Artist (varsa)
        if ($this->artist) {
            $artistName = $this->artist->getTranslated('name', $locale);
            if ($artistName) {
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $artistName,
                    'item' => $this->artist->getUrl($locale)
                ];
            }
        }

        // 4. Current Album
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
}
