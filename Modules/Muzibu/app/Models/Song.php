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

class Song extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes, HasFavorites, Searchable;

    protected $table = 'muzibu_songs';
    protected $primaryKey = 'song_id';

    /**
     * Dinamik connection resolver
     * Central tenant ise mysql (default), değilse tenant connection
     */
    public function getConnectionName()
    {
        // Eğer tenant context'i varsa ve central değilse tenant connection kullan
        if (function_exists('tenant') && tenant() && !tenant()->central) {
            return 'tenant';
        }

        // Central tenant veya tenant yok ise default connection
        return config('database.default');
    }

    protected $fillable = [
        'album_id',
        'genre_id',
        'title',
        'slug',
        'lyrics',
        'duration',
        'file_path',
        'hls_path',
        'encryption_key',
        'encryption_iv',
        'media_id',
        'is_featured',
        'play_count',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'lyrics' => 'array',
        'duration' => 'integer',
        'is_featured' => 'boolean',
        'play_count' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'lyrics'];

    /**
     * ID accessor - song_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->song_id;
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
     * Öne çıkan şarkıları getir
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * En çok dinlenenleri getir
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('play_count', 'desc')->limit($limit);
    }

    /**
     * Albüm ilişkisi
     */
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }

    /**
     * Tür ilişkisi
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id', 'genre_id');
    }

    /**
     * Sanatçı ilişkisi (album üzerinden)
     */
    public function artist()
    {
        return $this->hasOneThrough(
            Artist::class,
            Album::class,
            'album_id',    // Foreign key on albums table
            'artist_id',   // Foreign key on artists table
            'album_id',    // Local key on songs table
            'artist_id'    // Local key on albums table
        );
    }

    /**
     * Playlist'ler ilişkisi (many-to-many)
     */
    public function playlists()
    {
        return $this->belongsToMany(
            Playlist::class,
            'muzibu_playlist_song',
            'song_id',
            'playlist_id',
            'song_id',
            'playlist_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Dinleme kayıtları ilişkisi
     */
    public function plays()
    {
        return $this->hasMany(SongPlay::class, 'song_id', 'song_id');
    }

    /**
     * Thumbmaker media ilişkisi (Song cover)
     * Not: Spatie'nin media() methodu ile çakışmamak için coverMedia() kullanıyoruz
     */
    public function coverMedia()
    {
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, 'media_id');
    }

    /**
     * Song cover URL'i (Thumbmaker helper ile)
     * Önce kendi media_id'sine bakar, yoksa albümün media_id'sini kullanır
     */
    public function getCoverUrl(?int $width = 600, ?int $height = 600): ?string
    {
        // Önce kendi görseli var mı kontrol et
        if ($this->media_id && $this->coverMedia) {
            return thumb($this->coverMedia, $width, $height);
        }

        // Yoksa albümün görselini kullan
        if ($this->album && $this->album->media_id && $this->album->coverMedia) {
            return thumb($this->album->coverMedia, $width, $height);
        }

        return null;
    }

    /**
     * Dinleme sayısını artır
     */
    public function incrementPlayCount(?int $userId = null, ?string $ipAddress = null): void
    {
        // Play count artır
        $this->increment('play_count');

        // SongPlay kaydı oluştur
        $this->plays()->create([
            'user_id' => $userId,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => $this->detectDeviceType(),
        ]);
    }

    /**
     * Cihaz tipini tespit et
     */
    protected function detectDeviceType(): ?string
    {
        $userAgent = request()->userAgent();

        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
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
        $lyrics = $this->getTranslated('lyrics', app()->getLocale());

        if ($lyrics) {
            return \Illuminate\Support\Str::limit(strip_tags($lyrics), 160);
        }

        // Fallback: Genre + Artist + Duration
        $parts = [];

        if ($this->genre) {
            $parts[] = $this->genre->getTranslated('title', app()->getLocale());
        }

        if ($this->album && $this->album->artist) {
            $parts[] = $this->album->artist->getTranslated('title', app()->getLocale());
        }

        $parts[] = $this->getFormattedDuration();

        return implode(' - ', array_filter($parts));
    }

    public function getSeoFallbackKeywords(): array
    {
        $keywords = [];

        // Song title
        $title = $this->getSeoFallbackTitle();
        if ($title) {
            $words = array_filter(explode(' ', strtolower($title)), fn($word) => strlen($word) > 3);
            $keywords = array_merge($keywords, array_slice($words, 0, 2));
        }

        // Artist name
        if ($this->album && $this->album->artist) {
            $artistName = $this->album->artist->getTranslated('title', app()->getLocale());
            if ($artistName) {
                $keywords[] = strtolower($artistName);
            }
        }

        // Genre
        if ($this->genre) {
            $genreName = $this->genre->getTranslated('title', app()->getLocale());
            if ($genreName) {
                $keywords[] = strtolower($genreName);
            }
        }

        return array_slice(array_unique($keywords), 0, 5);
    }

    public function getSeoFallbackCanonicalUrl(): ?string
    {
        $slug = $this->getTranslated('slug', app()->getLocale());
        return $slug ? url('/muzibu/song/' . ltrim($slug, '/')) : null;
    }

    public function getSeoFallbackImage(): ?string
    {
        // Song cover
        if ($this->media) {
            return $this->media->getUrl();
        }

        // Album cover fallback
        if ($this->album && $this->album->media) {
            return $this->album->media->getUrl();
        }

        // Artist photo fallback
        if ($this->album && $this->album->artist && $this->album->artist->media) {
            return $this->album->artist->media->getUrl();
        }

        return null;
    }

    public function getSeoFallbackSchemaMarkup(): ?array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MusicRecording',
            'name' => $this->getSeoFallbackTitle(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'image' => $this->getSeoFallbackImage(),
            'duration' => 'PT' . $this->duration . 'S', // ISO 8601 duration format
        ];

        // Add artist (byArtist)
        if ($this->album && $this->album->artist) {
            $schema['byArtist'] = [
                '@type' => 'MusicGroup',
                'name' => $this->album->artist->getTranslated('title', app()->getLocale()),
                'url' => $this->album->artist->getSeoFallbackCanonicalUrl(),
            ];
        }

        // Add album (inAlbum)
        if ($this->album) {
            $schema['inAlbum'] = [
                '@type' => 'MusicAlbum',
                'name' => $this->album->getTranslated('title', app()->getLocale()),
                'url' => $this->album->getSeoFallbackCanonicalUrl(),
            ];
        }

        // Add genre
        if ($this->genre) {
            $schema['genre'] = $this->genre->getTranslated('title', app()->getLocale());
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
            'lyrics' => 'html',
            'slug' => 'auto',
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info("Song çevirisi tamamlandı", [
            'song_id' => $this->song_id,
            'album_id' => $this->album_id,
            'genre_id' => $this->genre_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'song_id';
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
            'audio' => [
                'type' => 'audio',
                'single_file' => true,
                'max_items' => 1,
                'max_size' => config('modules.media.max_audio_size', 102400), // 100MB
                'allowed_types' => ['mp3', 'wav', 'flac', 'm4a', 'ogg'],
                'sortable' => false,
            ],
        ];
    }

    /**
     * Song için locale-aware URL
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            return url("/song/{$slug}");
        }

        return url("/{$locale}/song/{$slug}");
    }

    /**
     * Get audio file URL (original MP3)
     */
    public function getAudioUrl(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return \Storage::disk('public')->url($this->file_path);
    }

    /**
     * Get HLS playlist URL (if converted)
     */
    public function getHlsUrl(): ?string
    {
        if (!$this->hls_path) {
            return null;
        }

        return \Storage::disk('public')->url($this->hls_path);
    }

    /**
     * Check if HLS conversion is needed
     */
    public function needsHlsConversion(): bool
    {
        return $this->file_path && !$this->hls_path;
    }

    /**
     * Check if song is encrypted
     */
    public function isEncrypted(): bool
    {
        return !empty($this->encryption_key);
    }

    /**
     * Get formatted duration (MM:SS)
     */
    public function getFormattedDuration(): string
    {
        if (!$this->duration) {
            return '00:00';
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Extract bitrate from audio file using getID3 (runtime)
     */
    public function getBitrate(): int
    {
        if (!$this->file_path) {
            return 256; // Fallback
        }

        try {
            $getID3 = new \getID3;
            $filePath = file_exists($this->file_path) ? $this->file_path : \Storage::disk('public')->path($this->file_path);

            if (!file_exists($filePath)) {
                return 256; // Fallback
            }

            $fileInfo = $getID3->analyze($filePath);

            // Extract bitrate (convert from bps to kbps)
            if (isset($fileInfo['audio']['bitrate'])) {
                return (int) ($fileInfo['audio']['bitrate'] / 1000);
            }

            return 256; // Fallback
        } catch (\Exception $e) {
            \Log::warning('Muzibu: Bitrate extraction failed, using fallback', [
                'song_id' => $this->song_id,
                'error' => $e->getMessage()
            ]);

            return 256; // Fallback
        }
    }

    /**
     * Get formatted bitrate (e.g., "320 kbps")
     */
    public function getFormattedBitrate(): string
    {
        return $this->getBitrate() . ' kbps';
    }

    /**
     * Get the indexable data array for the model (Meilisearch)
     */
    public function toSearchableArray(): array
    {
        // Get active languages from tenant_languages table
        // Fallback to default tenant connection if no tenant context
        try {
            $connection = (tenant() && !tenant()->central) ? 'tenant' : 'mysql';
            $langCodes = \DB::connection($connection)
                ->table('tenant_languages')
                ->where('is_active', 1)
                ->pluck('code')
                ->toArray();
        } catch (\Exception $e) {
            // Fallback: Use default languages if table doesn't exist
            $langCodes = ['tr', 'en'];
        }

        $data = [
            'id' => $this->song_id,
            'duration' => $this->duration,
            'play_count' => $this->play_count,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->timestamp,
        ];

        // Index all active languages
        foreach ($langCodes as $langCode) {
            $data["title_{$langCode}"] = $this->getTranslated('title', $langCode);
            $data["lyrics_{$langCode}"] = $this->getTranslated('lyrics', $langCode);
        }

        // Related data
        if ($this->album) {
            foreach ($langCodes as $langCode) {
                $data["album_title_{$langCode}"] = $this->album->getTranslated('title', $langCode);
            }

            if ($this->album->artist) {
                foreach ($langCodes as $langCode) {
                    $data["artist_title_{$langCode}"] = $this->album->artist->getTranslated('title', $langCode);
                }
            }
        }

        if ($this->genre) {
            foreach ($langCodes as $langCode) {
                $data["genre_title_{$langCode}"] = $this->genre->getTranslated('title', $langCode);
            }
        }

        return $data;
    }

    /**
     * Get the index name for the model (tenant-aware)
     */
    public function searchableAs(): string
    {
        $tenantId = tenant() ? tenant()->id : 'central';
        return "tenant_{$tenantId}_songs";
    }

    /**
     * Get the value used to index the model (primary key)
     */
    public function getScoutKey()
    {
        return $this->song_id;
    }

    /**
     * Get the key name used to index the model
     */
    public function getScoutKeyName()
    {
        return 'song_id';
    }
}
