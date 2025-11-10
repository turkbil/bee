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

class Song extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'muzibu_songs';
    protected $primaryKey = 'song_id';

    protected $fillable = [
        'album_id',
        'genre_id',
        'title',
        'slug',
        'lyrics',
        'duration',
        'file_path',
        'hls_path',
        'hls_converted',
        'bitrate',
        'metadata',
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
        'bitrate' => 'integer',
        'metadata' => 'array',
        'hls_converted' => 'boolean',
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
        return $this->belongsTo(\Modules\MediaManagement\App\Models\Media::class, 'media_id');
    }

    /**
     * Song cover URL'i (Thumbmaker helper ile)
     */
    public function getCoverUrl(?int $width = 600, ?int $height = 600): ?string
    {
        if (!$this->media_id) {
            return null;
        }
        return thumb($this->coverMedia, $width, $height);
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
            'cover' => [
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
            return url("/muzibu/song/{$slug}");
        }

        return url("/{$locale}/muzibu/song/{$slug}");
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
        if (!$this->hls_converted || !$this->hls_path) {
            return null;
        }

        return \Storage::disk('public')->url($this->hls_path);
    }

    /**
     * Check if HLS conversion is needed
     */
    public function needsHlsConversion(): bool
    {
        return $this->file_path && !$this->hls_converted;
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
     * Get formatted bitrate (e.g., "320 kbps")
     */
    public function getFormattedBitrate(): ?string
    {
        if (!$this->bitrate) {
            return null;
        }

        return $this->bitrate . ' kbps';
    }

    /**
     * Extract metadata using getID3
     */
    public function extractMetadata(): bool
    {
        if (!$this->file_path) {
            return false;
        }

        try {
            $getID3 = new \getID3;
            $filePath = \Storage::disk('public')->path($this->file_path);

            if (!file_exists($filePath)) {
                return false;
            }

            $fileInfo = $getID3->analyze($filePath);

            // Extract metadata
            $this->duration = isset($fileInfo['playtime_seconds']) ? (int) $fileInfo['playtime_seconds'] : null;
            $this->bitrate = isset($fileInfo['audio']['bitrate']) ? (int) ($fileInfo['audio']['bitrate'] / 1000) : null;

            // Store additional metadata
            $this->metadata = [
                'sample_rate' => $fileInfo['audio']['sample_rate'] ?? null,
                'channels' => $fileInfo['audio']['channels'] ?? null,
                'channel_mode' => $fileInfo['audio']['channelmode'] ?? null,
                'filesize' => $fileInfo['filesize'] ?? null,
                'mime_type' => $fileInfo['mime_type'] ?? null,
            ];

            $this->save();

            return true;
        } catch (\Exception $e) {
            \Log::error('Muzibu: Metadata extraction failed', [
                'song_id' => $this->song_id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
