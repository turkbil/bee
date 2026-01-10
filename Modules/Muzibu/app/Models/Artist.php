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
use Modules\ReviewSystem\App\Traits\HasReviews;
use Modules\Muzibu\App\Traits\HasCachedCounts;

class Artist extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasUniversalSchemas, HasFactory, HasMediaManagement, SoftDeletes, HasReviews, Searchable, HasCachedCounts;

    protected $table = 'muzibu_artists';
    protected $primaryKey = 'artist_id';
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
        'bio',
        'media_id',
        'is_active',
    ];

    protected $casts = [
        // NOT: title, slug, bio CAST'LANMAMALI!
        // HasTranslations trait bunları otomatik yönetiyor
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'bio'];

    /**
     * ID accessor - artist_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->artist_id;
    }

    /**
     * Sluggable Ayarları - JSON çoklu dil desteği için devre dışı
     * HasTranslations trait'inde generateSlugForLocale() kullanılacak
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
     * Albüm ilişkisi
     */
    public function albums()
    {
        return $this->hasMany(Album::class, 'artist_id', 'artist_id');
    }

    /**
     * Şarkı ilişkisi (albüm üzerinden)
     */
    public function songs()
    {
        return $this->hasManyThrough(Song::class, Album::class, 'artist_id', 'album_id', 'artist_id', 'album_id');
    }

    /**
     * HasCachedCounts configuration
     * Defines cached count fields and their calculators
     */
    protected function getCachedCountsConfig(): array
    {
        return [
            'albums_count' => fn() => $this->albums()->where('is_active', true)->count(),
            'songs_count' => fn() => $this->songs()->where('muzibu_songs.is_active', true)->count(),
            'total_duration' => fn() => $this->songs()->where('muzibu_songs.is_active', true)->sum('muzibu_songs.duration'),
        ];
    }

    /**
     * Albums count accessor (cached)
     */
    public function getAlbumsCountAttribute(): int
    {
        return $this->getCachedCount('albums_count');
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
     * Thumbmaker media ilişkisi (Artist photo)
     * Not: Spatie'nin media() methodu ile çakışmamak için photoMedia() kullanıyoruz
     */
    public function photoMedia()
    {
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'media_id');
    }

    /**
     * Artist photo URL'i (Thumbmaker helper ile)
     * Sadece Spatie hero collection kullanır
     */
    public function getPhotoUrl(?int $width = 400, ?int $height = 400): ?string
    {
        $heroMedia = $this->getFirstMedia('hero');
        return $heroMedia ? thumb($heroMedia, $width, $height, ['scale' => 1]) : null;
    }

    /**
     * Alias for getPhotoUrl - Search compatibility
     */
    public function getCoverUrl(?int $width = 400, ?int $height = 400): ?string
    {
        return $this->getPhotoUrl($width, $height);
    }

    /**
     * Photo URL accessor (kare, thumbmaker ile)
     * Frontend için otomatik square photo (200x200, ortadan kırp)
     */
    public function getPhotoUrlAttribute(): string
    {
        $heroMedia = $this->getFirstMedia('hero');
        return $heroMedia ? thumb($heroMedia, 200, 200, ['scale' => 1]) : '';
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
        $bio = $this->getTranslated('bio', app()->getLocale());
        return $bio ? \Illuminate\Support\Str::limit(strip_tags($bio), 160) : null;
    }

    public function getSeoFallbackKeywords(): array
    {
        $title = $this->getSeoFallbackTitle();
        if ($title) {
            $words = array_filter(explode(' ', strtolower($title)), fn($word) => strlen($word) > 3);
            return array_slice($words, 0, 5);
        }
        return [];
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
            '@type' => 'MusicGroup',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'image' => $this->getSeoFallbackImage(),
        ];

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
            'bio' => 'html',
            'slug' => 'auto',
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info("Artist çevirisi tamamlandı", [
            'artist_id' => $this->artist_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'artist_id';
    }

    /**
     * Media collections config
     */
    protected function getMediaConfig(): array
    {
        return [
            'photo' => [
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
     * Artist için locale-aware URL
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            return url("/artists/{$slug}");
        }

        return url("/{$locale}/artists/{$slug}");
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

        $data = ['id' => $this->artist_id, 'is_active' => $this->is_active, 'created_at' => $this->created_at?->timestamp];

        foreach ($langCodes as $langCode) {
            $data["title_{$langCode}"] = $this->getTranslated('title', $langCode);
            if (method_exists($this, 'description')) {
                $data["description_{$langCode}"] = $this->getTranslated('description', $langCode);
            }
        }

        return $data;
    }

    public function searchableAs(): string
    {
        $tenantId = tenant() ? tenant()->id : 1001;
        return "tenant_{$tenantId}_artists";
    }

    public function getScoutKey()
    {
        return $this->artist_id;
    }

    public function getScoutKeyName()
    {
        return 'artist_id';
    }

    // ========================================
    // 🎤 Schema.org Implementation
    // ========================================

    /**
     * Get all schemas for this artist (MusicGroup + Breadcrumb + FAQ + HowTo)
     *
     * @return array
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. MusicGroup Schema (Primary)
        $musicGroupSchema = $this->getMusicGroupSchema();
        if ($musicGroupSchema) {
            $schemas['musicGroup'] = $musicGroupSchema;
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
     * Generate MusicGroup Schema
     *
     * @return array|null
     */
    protected function getMusicGroupSchema(): ?array
    {
        $locale = app()->getLocale();
        $name = $this->getTranslated('title', $locale); // NOT: Artist'te title var, name yok!

        if (!$name) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MusicGroup',
            'name' => $name,
            'url' => $this->getUrl($locale),
        ];

        // Bio/Description
        $bio = $this->getTranslated('bio', $locale);
        if ($bio) {
            $schema['description'] = strip_tags($bio);
        }

        // Artist photo
        $heroMedia = $this->getFirstMedia('hero');
        if ($heroMedia) {
            $schema['image'] = thumb($heroMedia, 1200, 1200, ['quality' => 90]);
        }

        // Number of albums
        $albumsCount = $this->getAlbumsCountAttribute();
        if ($albumsCount > 0) {
            $schema['numAlbums'] = $albumsCount;

            // Album list (first 10 albums for schema)
            $albums = $this->albums()->where('is_active', true)->limit(10)->get();
            if ($albums->count() > 0) {
                $albumList = [];
                foreach ($albums as $album) {
                    $albumTitle = $album->getTranslated('title', $locale);
                    if ($albumTitle) {
                        $albumList[] = [
                            '@type' => 'MusicAlbum',
                            'name' => $albumTitle,
                            'url' => $album->getUrl($locale)
                        ];
                    }
                }
                if (!empty($albumList)) {
                    $schema['album'] = $albumList;
                }
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
     * Structure: Home → Artists → Current Artist
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

        // 2. Artists Ana Sayfa
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Muzibu', 'artists.index');
        $artistsIndexUrl = $locale === get_tenant_default_locale()
            ? url("/{$moduleSlug}")
            : url("/{$locale}/{$moduleSlug}");

        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Sanatçılar',
            'item' => $artistsIndexUrl
        ];

        // 3. Current Artist
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
