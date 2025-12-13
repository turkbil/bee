<?php

namespace Modules\Muzibu\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use App\Contracts\TranslatableEntity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Modules\Favorite\App\Traits\HasFavorites;

class Album extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes, HasFavorites, Searchable;

    protected $table = 'muzibu_albums';
    protected $primaryKey = 'album_id';

    /**
     * Dinamik connection resolver
     * Central tenant ise mysql (default), değilse tenant connection
     */
    public function getConnectionName()
    {
        if (function_exists('tenant') && tenant() && !tenant()->central) {
            return 'tenant';
        }
        return config('database.default');
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
        'title' => 'array',
        'slug' => 'array',
        'description' => 'array',
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
     * Toplam süreyi hesapla
     */
    public function getTotalDuration(): int
    {
        return $this->songs->sum('duration');
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
     */
    public function getCoverUrl(?int $width = 800, ?int $height = 800): ?string
    {
        if (!$this->media_id) {
            return null;
        }
        return thumb($this->coverMedia, $width, $height);
    }

    /**
     * Cover URL accessor (kare, thumbmaker ile)
     * Frontend için otomatik square cover (200x200, ortadan kırp)
     */
    public function getCoverUrlAttribute(): string
    {
        $coverUrl = $this->getFirstMediaUrl('hero');

        if (empty($coverUrl)) {
            return '';
        }

        // Thumbmaker ile kare cover (200x200, fill/crop mode - ortadan kırpar)
        return thumb($coverUrl, 200, 200, ['scale' => 1]);
    }

    /**
     * Player blur background (album kapağından blur arka plan)
     * Orta kısımdan ince şerit alıp çok blur yapar
     */
    public function getBlurBackgroundAttribute(): ?string
    {
        if (!$this->media_id || !$this->coverMedia) {
            return null;
        }

        // 1200x200 ince şerit, orta kısımdan crop, çok blur
        return thumb($this->coverMedia, 1200, 200, [
            'fit' => 'crop',
            'blur' => 80
        ]);
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
        return $this->media?->getUrl() ?? null;
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
            return url("/muzibu/album/{$slug}");
        }

        return url("/{$locale}/muzibu/album/{$slug}");
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

        return $data;
    }

    public function searchableAs(): string
    {
        $tenantId = tenant() ? tenant()->id : 'central';
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
}
