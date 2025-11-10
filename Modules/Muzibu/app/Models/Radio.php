<?php

namespace Modules\Muzibu\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use App\Contracts\TranslatableEntity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;

class Radio extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'muzibu_radios';
    protected $primaryKey = 'radio_id';

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
     * Thumbmaker media ilişkisi (Radio logo)
     * Not: Spatie'nin media() methodu ile çakışmamak için logoMedia() kullanıyoruz
     */
    public function logoMedia()
    {
        return $this->belongsTo(\Modules\MediaManagement\App\Models\Media::class, 'media_id');
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
        $slug = $this->getTranslated('slug', app()->getLocale());
        return $slug ? url('/muzibu/radio/' . ltrim($slug, '/')) : null;
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
}
