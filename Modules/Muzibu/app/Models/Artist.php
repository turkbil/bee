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
use Modules\ReviewSystem\App\Traits\HasReviews;

class Artist extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes, HasReviews, Searchable;

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
        'title' => 'array',
        'slug' => 'array',
        'bio' => 'array',
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
     * Thumbmaker media ilişkisi (Artist photo)
     * Not: Spatie'nin media() methodu ile çakışmamak için photoMedia() kullanıyoruz
     */
    public function photoMedia()
    {
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'media_id');
    }

    /**
     * Artist photo URL'i (Thumbmaker helper ile)
     */
    public function getPhotoUrl(?int $width = 400, ?int $height = 400): ?string
    {
        if (!$this->media_id) {
            return null;
        }
        return thumb($this->photoMedia, $width, $height, ['scale' => 1]);
    }

    /**
     * Photo URL accessor (kare, thumbmaker ile)
     * Frontend için otomatik square photo (200x200, ortadan kırp)
     */
    public function getPhotoUrlAttribute(): string
    {
        $photoUrl = $this->getFirstMediaUrl('photo');

        if (empty($photoUrl)) {
            // Eğer Spatie photo yoksa, media_id kullan
            if ($this->media_id && $this->photoMedia) {
                return thumb($this->photoMedia, 200, 200, ['scale' => 1]);
            }
            return '';
        }

        // Thumbmaker ile kare photo (200x200, fill/crop mode - ortadan kırpar)
        return thumb($photoUrl, 200, 200, ['scale' => 1]);
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
        return $this->media?->getUrl() ?? null;
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
     * Tüm schema'ları al (MusicGroup + Breadcrumb)
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. MusicGroup Schema
        $artistSchema = $this->getSchemaMarkup();
        if ($artistSchema) {
            $schemas['musicgroup'] = $artistSchema;
        }

        // 2. Breadcrumb Schema
        if (method_exists($this, 'getBreadcrumbSchema')) {
            $breadcrumbSchema = $this->getBreadcrumbSchema();
            if ($breadcrumbSchema) {
                $schemas['breadcrumb'] = $breadcrumbSchema;
            }
        }

        return $schemas;
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
            return url("/muzibu/artist/{$slug}");
        }

        return url("/{$locale}/muzibu/artist/{$slug}");
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
}
