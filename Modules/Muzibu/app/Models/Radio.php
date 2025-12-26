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

class Radio extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes, Searchable, HasFavorites;

    protected $table = 'muzibu_radios';
    protected $primaryKey = 'radio_id';
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
        'media_id',
        'is_active',
    ];

    protected $casts = [
        // NOT: title, slug CAST'LANMAMALI!
        // HasTranslations trait bunları otomatik yönetiyor
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
     * ID accessor - radio_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->radio_id;
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
     * Sektörler ilişkisi (many-to-many)
     */
    public function sectors()
    {
        return $this->belongsToMany(
            Sector::class,
            'muzibu_radio_sector',
            'radio_id',
            'sector_id',
            'radio_id',
            'sector_id'
        );
    }

    /**
     * Playlist'ler ilişkisi (many-to-many)
     */
    public function playlists()
    {
        return $this->belongsToMany(
            Playlist::class,
            'muzibu_playlist_radio',
            'radio_id',
            'playlist_id',
            'radio_id',
            'playlist_id'
        );
    }

    /**
     * Toplam süreyi hesapla (tüm playlist'lerdeki şarkıların toplamı)
     */
    public function getTotalDuration(): int
    {
        $totalSeconds = 0;
        foreach ($this->playlists as $playlist) {
            $totalSeconds += $playlist->getTotalDuration();
        }
        return $totalSeconds;
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
     * Toplam şarkı sayısını hesapla (tüm playlist'lerdeki şarkıların toplamı)
     */
    public function getTotalSongsCount(): int
    {
        $totalSongs = 0;
        foreach ($this->playlists as $playlist) {
            $totalSongs += $playlist->songs->count();
        }
        return $totalSongs;
    }

    /**
     * Thumbmaker media ilişkisi (Radio logo)
     * Not: Spatie'nin media() methodu ile çakışmamak için logoMedia() kullanıyoruz
     */
    public function logoMedia()
    {
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'media_id');
    }

    /**
     * Radio logo URL'i (Thumbmaker helper ile)
     */
    public function getLogoUrl(?int $width = 400, ?int $height = 400): ?string
    {
        if (!$this->media_id) {
            return null;
        }
        return thumb($this->logoMedia, $width, $height);
    }

    /**
     * Alias for getLogoUrl - Search compatibility
     */
    public function getCoverUrl(?int $width = 400, ?int $height = 400): ?string
    {
        return $this->getLogoUrl($width, $height);
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
        $title = $this->getSeoFallbackTitle();
        $sectorsCount = $this->sectors()->count();

        return "{$title} - {$sectorsCount} sektör için özel müzik yayını";
    }

    public function getSeoFallbackKeywords(): array
    {
        $keywords = [];

        // Radio title
        $title = $this->getSeoFallbackTitle();
        if ($title) {
            $words = array_filter(explode(' ', strtolower($title)), fn($word) => strlen($word) > 3);
            $keywords = array_merge($keywords, array_slice($words, 0, 3));
        }

        // Sectors
        $sectors = $this->sectors->pluck('title')->take(2);
        foreach ($sectors as $sector) {
            if ($sector) {
                $keywords[] = strtolower($sector);
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
        return [
            '@context' => 'https://schema.org',
            '@type' => 'RadioStation',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'image' => $this->getSeoFallbackImage(),
        ];
    }

    /**
     * TranslatableEntity interface implementation
     */
    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
            'slug' => 'auto',
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info("Radio çevirisi tamamlandı", [
            'radio_id' => $this->radio_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'radio_id';
    }

    /**
     * Media collections config
     */
    protected function getMediaConfig(): array
    {
        return [
            'logo' => [
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
     * Radio için locale-aware URL
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            return url("/muzibu/radio/{$slug}");
        }

        return url("/{$locale}/muzibu/radio/{$slug}");
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

        $data = ['id' => $this->radio_id, 'is_active' => $this->is_active, 'created_at' => $this->created_at?->timestamp];

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
        return "tenant_{$tenantId}_radios";
    }

    public function getScoutKey()
    {
        return $this->radio_id;
    }

    public function getScoutKeyName()
    {
        return 'radio_id';
    }
}
