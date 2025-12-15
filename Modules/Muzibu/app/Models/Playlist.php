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

class Playlist extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes, HasFavorites, Searchable;

    protected $table = 'muzibu_playlists';
    protected $primaryKey = 'playlist_id';
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
        'user_id',
        'title',
        'slug',
        'description',
        'media_id',
        'is_system',
        'is_public',
        'is_radio',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'description' => 'array',
        'is_system' => 'boolean',
        'is_public' => 'boolean',
        'is_radio' => 'boolean',
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
     * ID accessor - playlist_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->playlist_id;
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
     * Public playlist'leri getir
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * System playlist'leri getir
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * User playlist'leri getir
     */
    public function scopeUserPlaylists($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Radio type playlist'leri getir
     */
    public function scopeRadios($query)
    {
        return $query->where('is_radio', true);
    }

    /**
     * Kullanıcı ilişkisi
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Şarkılar ilişkisi (many-to-many with position)
     */
    public function songs()
    {
        return $this->belongsToMany(
            Song::class,
            'muzibu_playlist_song',
            'playlist_id',
            'song_id',
            'playlist_id',
            'song_id'
        )->withPivot('position')->withTimestamps()->orderBy('muzibu_playlist_song.position');
    }

    /**
     * Sektörler ilişkisi (many-to-many)
     */
    public function sectors()
    {
        return $this->belongsToMany(
            Sector::class,
            'muzibu_playlist_sector',
            'playlist_id',
            'sector_id',
            'playlist_id',
            'sector_id'
        );
    }

    /**
     * Radyolar ilişkisi (many-to-many)
     */
    public function radios()
    {
        return $this->belongsToMany(
            Radio::class,
            'muzibu_playlist_radio',
            'playlist_id',
            'radio_id',
            'playlist_id',
            'radio_id'
        );
    }

    /**
     * Thumbmaker media ilişkisi (Playlist cover)
     * Not: Spatie'nin media() methodu ile çakışmamak için coverMedia() kullanıyoruz
     */
    public function coverMedia()
    {
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'media_id');
    }

    /**
     * Playlist cover URL'i (Thumbmaker helper ile)
     */
    public function getCoverUrl(?int $width = 600, ?int $height = 600): ?string
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
     * Toplam süreyi hesapla
     */
    public function getTotalDuration(): int
    {
        return $this->songs->sum('duration');
    }

    /**
     * Formatlanmış toplam süre (HH:MM:SS)
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
     * Şarkı sayısı
     */
    public function getSongsCount(): int
    {
        return $this->songs()->count();
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

        if ($description) {
            return \Illuminate\Support\Str::limit(strip_tags($description), 160);
        }

        // Fallback: Song count + Duration
        $songsCount = $this->getSongsCount();
        $duration = $this->getFormattedTotalDuration();

        return "{$songsCount} şarkı - {$duration}";
    }

    public function getSeoFallbackKeywords(): array
    {
        $keywords = [];

        // Playlist title
        $title = $this->getSeoFallbackTitle();
        if ($title) {
            $words = array_filter(explode(' ', strtolower($title)), fn($word) => strlen($word) > 3);
            $keywords = array_merge($keywords, array_slice($words, 0, 3));
        }

        // Genre from songs
        $genres = $this->songs->pluck('genre.title')->unique()->take(2);
        foreach ($genres as $genre) {
            if ($genre) {
                $keywords[] = strtolower($genre);
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
            '@type' => 'MusicPlaylist',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'image' => $this->getSeoFallbackImage(),
            'numTracks' => $this->getSongsCount(),
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
        \Log::info("Playlist çevirisi tamamlandı", [
            'playlist_id' => $this->playlist_id,
            'user_id' => $this->user_id,
            'is_system' => $this->is_system,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'playlist_id';
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
     * Playlist için locale-aware URL
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            return url("/muzibu/playlist/{$slug}");
        }

        return url("/{$locale}/muzibu/playlist/{$slug}");
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
            'id' => $this->playlist_id,
            'is_active' => $this->is_active,
            'is_public' => $this->is_public,
            'is_system' => $this->is_system,
            'is_radio' => $this->is_radio,
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
        $tenantId = tenant() ? tenant()->id : 'central';
        return "tenant_{$tenantId}_playlists";
    }

    public function getScoutKey()
    {
        return $this->playlist_id;
    }

    public function getScoutKeyName()
    {
        return 'playlist_id';
    }
}
