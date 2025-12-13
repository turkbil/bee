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

class Genre extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes, Searchable;

    protected $table = 'muzibu_genres';
    protected $primaryKey = 'genre_id';
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
     * ID accessor - genre_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->genre_id;
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
     * Şarkı ilişkisi
     */
    public function songs()
    {
        return $this->hasMany(Song::class, 'genre_id', 'genre_id');
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
     * Thumbmaker media ilişkisi (Genre icon/image)
     * Not: Spatie'nin media() methodu ile çakışmamak için iconMedia() kullanıyoruz
     */
    public function iconMedia()
    {
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'media_id');
    }

    /**
     * Genre icon URL'i (Thumbmaker helper ile)
     */
    public function getIconUrl(?int $width = 300, ?int $height = 300): ?string
    {
        if (!$this->media_id) {
            return null;
        }
        return thumb($this->iconMedia, $width, $height);
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
        return [
            '@context' => 'https://schema.org',
            '@type' => 'MusicGenre',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
        ];
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
        \Log::info("Genre çevirisi tamamlandı", [
            'genre_id' => $this->genre_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'genre_id';
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
     * Genre için locale-aware URL
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            return url("/muzibu/genre/{$slug}");
        }

        return url("/{$locale}/muzibu/genre/{$slug}");
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

        $data = ['id' => $this->genre_id, 'is_active' => $this->is_active, 'created_at' => $this->created_at?->timestamp];

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
        $tenantId = tenant() ? tenant()->id : 'central';
        return "tenant_{$tenantId}_genres";
    }

    public function getScoutKey()
    {
        return $this->genre_id;
    }

    public function getScoutKeyName()
    {
        return 'genre_id';
    }
}
