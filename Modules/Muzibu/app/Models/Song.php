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

class Song extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasUniversalSchemas, HasFactory, HasMediaManagement, SoftDeletes, HasFavorites, HasReviews, Searchable;

    protected $table = 'muzibu_songs';
    protected $primaryKey = 'song_id';

    /**
     * Dinamik connection resolver
     * Muzibu modülü SADECE tenant 1001 için, ZORLA tenant connection kullan!
     */
    public function getConnectionName()
    {
        // ✅ Muzibu modülü tenant-specific, ZORLA tenant connection!
        // Tenant 1001 (muzibu) için ayrı database var
        return 'tenant';
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
        'color_hash',
        'is_active',
    ];

    protected $casts = [
        // NOT: title, slug, lyrics CAST'LANMAMALI!
        // HasTranslations trait bunları otomatik yönetiyor
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
     * Spatie Media Collections - hero ve audio tek dosya (yeni yüklenince eski silinir)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero')
            ->singleFile();

        $this->addMediaCollection('audio')
            ->singleFile();
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
     * Önce kendi hero'su, yoksa albümün hero'su
     */
    public function getCoverUrl(?int $width = 600, ?int $height = 600, int $quality = 90): ?string
    {
        // 1. Önce kendi Spatie hero collection kontrol et
        $heroMedia = $this->getFirstMedia('hero');
        if ($heroMedia) {
            return thumb($heroMedia, $width, $height, ['quality' => $quality, 'scale' => 1]);
        }

        // 2. Yoksa albümün görselini kullan (albüm de hero kullanıyor)
        if ($this->album) {
            return $this->album->getCoverUrl($width, $height, $quality);
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
        // Use existing getUrl() method for consistency
        return $this->getUrl();
    }

    public function getSeoFallbackImage(): ?string
    {
        // Song cover (Spatie media collection)
        $heroUrl = $this->getFirstMediaUrl('hero');
        if ($heroUrl) {
            return $heroUrl;
        }

        // Album cover fallback
        if ($this->album) {
            $albumHeroUrl = $this->album->getFirstMediaUrl('hero');
            if ($albumHeroUrl) {
                return $albumHeroUrl;
            }
        }

        // Artist photo fallback
        if ($this->album && $this->album->artist) {
            $artistPhotoUrl = $this->album->artist->getFirstMediaUrl('photo');
            if ($artistPhotoUrl) {
                return $artistPhotoUrl;
            }
        }

        return null;
    }

    public function getSeoFallbackSchemaMarkup(): ?array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MusicRecording',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
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

        // ⭐ Aggregated Rating - HasReviews trait'inden alınır
        // Google guideline: Müzik platformları için kullanıcı rating'leri
        if (method_exists($this, 'averageRating') && method_exists($this, 'ratingsCount')) {
            $avgRating = $this->averageRating();
            $ratingCount = $this->ratingsCount();

            // Rating varsa ekle (HasReviews trait varsayılan 5 yıldız üretiyor)
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
            return url("/songs/{$slug}");
        }

        return url("/{$locale}/songs/{$slug}");
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
     * Tenant-aware file URL accessor (for admin player)
     * Returns: /storage/tenant1001/muzibu/songs/song_xxx.mp3
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        // Admin'de tenant-aware URL döndür
        $tenantId = tenant() ? tenant()->id : null;

        if ($tenantId) {
            return '/storage/tenant' . $tenantId . '/muzibu/songs/' . basename($this->file_path);
        }

        // Fallback: normal storage URL
        return '/storage/muzibu/songs/' . basename($this->file_path);
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
        // ✅ Müzibu modülü SADECE tenant 1001'de kullanılır
        // Tenant context yoksa fallback 1001 (Muzibu)
        $tenantId = tenant() ? tenant()->id : 1001;
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

    /**
     * Generate color hash from song title
     * Returns HSL values for gradient (format: "h1,s1,l1,h2,s2,l2,h3,s3,l3")
     * 3 independent colors with minimum 60° separation for variety
     *
     * @param string $title Song title
     * @return string Color hash (e.g., "45,85,55,165,70,50,285,80,45")
     */
    public static function generateColorHash(string $title): string
    {
        $normalizedTitle = mb_strtolower(trim($title));
        $md5 = md5($normalizedTitle);

        // 3 bağımsız hue seç, ama aralarında minimum 60° fark olsun
        $hues = [];
        $minDistance = 60;

        for ($i = 0; $i < 3; $i++) {
            $attempts = 0;
            do {
                // Her deneme için farklı hash bölümü kullan
                $h = hexdec(substr($md5, ($i * 4 + $attempts) % 28, 4)) % 360;
                $tooClose = false;

                foreach ($hues as $existingHue) {
                    $diff = abs($h - $existingHue);
                    $diff = min($diff, 360 - $diff); // Circular distance
                    if ($diff < $minDistance) {
                        $tooClose = true;
                        break;
                    }
                }
                $attempts++;
            } while ($tooClose && $attempts < 10);

            $hues[] = $h;
        }

        // Her renk için farklı saturation ve lightness
        $colors = [];
        for ($i = 0; $i < 3; $i++) {
            $h = $hues[$i];
            // Saturation: 60-95%
            $s = 60 + (hexdec(substr($md5, 12 + $i, 1)) % 36);
            // Lightness: 40-65%
            $l = 40 + (hexdec(substr($md5, 16 + $i, 1)) % 26);
            $colors[] = "{$h},{$s},{$l}";
        }

        return implode(',', $colors);
    }

    /**
     * Get or generate color hash for this song
     * If color_hash is null, generates and saves it
     *
     * @return string Color hash
     */
    public function getOrGenerateColorHash(): string
    {
        if ($this->color_hash) {
            return $this->color_hash;
        }

        // Get title (prefer Turkish, fallback to English or raw)
        $title = $this->getTranslated('title', 'tr')
            ?? $this->getTranslated('title', 'en')
            ?? $this->title
            ?? 'Untitled';

        $colorHash = self::generateColorHash($title);

        // Save to database
        $this->update(['color_hash' => $colorHash]);

        return $colorHash;
    }

    /**
     * Get CSS gradient string for player background
     * Returns a 3-color gradient CSS value
     *
     * @param int $saturation HSL saturation (default: 70)
     * @param int $lightness HSL lightness (default: 45)
     * @param int $angle Gradient angle in degrees (default: 135)
     * @return string CSS gradient (e.g., "linear-gradient(135deg, hsl(45, 70%, 45%), ...)")
     */
    public function getGradientCss(int $saturation = 70, int $lightness = 45, int $angle = 135): string
    {
        $colorHash = $this->getOrGenerateColorHash();
        $hues = explode(',', $colorHash);

        // Fallback if invalid format
        if (count($hues) !== 3) {
            $hues = [200, 240, 280];
        }

        return sprintf(
            'linear-gradient(%ddeg, hsl(%d, %d%%, %d%%), hsl(%d, %d%%, %d%%), hsl(%d, %d%%, %d%%))',
            $angle,
            (int) $hues[0], $saturation, $lightness,
            (int) $hues[1], $saturation, $lightness,
            (int) $hues[2], $saturation, $lightness
        );
    }

    /**
     * Boot method - Register model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate color_hash when creating a new song
        static::creating(function ($song) {
            if (empty($song->color_hash)) {
                $title = $song->title;

                // Handle JSON title (translations)
                if (is_array($title)) {
                    $title = $title['tr'] ?? $title['en'] ?? reset($title) ?? 'Untitled';
                } elseif (is_string($title) && str_starts_with($title, '{')) {
                    $decoded = json_decode($title, true);
                    $title = $decoded['tr'] ?? $decoded['en'] ?? reset($decoded) ?? 'Untitled';
                }

                $song->color_hash = self::generateColorHash($title ?: 'Untitled');
            }
        });
    }

    // ========================================
    // 🎵 Schema.org Implementation
    // ========================================

    /**
     * Get all schemas for this song (MusicRecording + Breadcrumb + FAQ + HowTo)
     *
     * @return array
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. MusicRecording Schema (Primary)
        $musicRecordingSchema = $this->getMusicRecordingSchema();
        if ($musicRecordingSchema) {
            $schemas['musicRecording'] = $musicRecordingSchema;
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
     * Generate MusicRecording Schema
     *
     * @return array|null
     */
    protected function getMusicRecordingSchema(): ?array
    {
        $locale = app()->getLocale();
        $title = $this->getTranslated('title', $locale);

        if (!$title) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MusicRecording',
            'name' => $title,
            'url' => $this->getUrl($locale),
        ];

        // Duration (ISO 8601 format: PT3M45S)
        if ($this->duration) {
            $minutes = floor($this->duration / 60);
            $seconds = $this->duration % 60;
            $schema['duration'] = sprintf('PT%dM%dS', $minutes, $seconds);
        }

        // Cover image
        $coverUrl = $this->getCoverUrl(1200, 1200);
        if ($coverUrl) {
            $schema['image'] = $coverUrl;
        }

        // Lyrics
        $lyrics = $this->getTranslated('lyrics', $locale);
        if ($lyrics) {
            $schema['lyrics'] = [
                '@type' => 'CreativeWork',
                'text' => $lyrics
            ];
        }

        // Genre
        if ($this->genre) {
            $genreName = $this->genre->getTranslated('name', $locale);
            if ($genreName) {
                $schema['genre'] = $genreName;
            }
        }

        // Album
        if ($this->album) {
            $albumTitle = $this->album->getTranslated('title', $locale);
            if ($albumTitle) {
                $schema['inAlbum'] = [
                    '@type' => 'MusicAlbum',
                    'name' => $albumTitle,
                    'url' => $this->album->getUrl($locale)
                ];
            }
        }

        // Artist (through album)
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
     * Structure: Home → Müzikler → Genre (if exists) → Album (if exists) → Current Song
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

        // 2. Müzikler Ana Sayfa
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Muzibu', 'songs.index');
        $songsIndexUrl = $locale === get_tenant_default_locale()
            ? url("/{$moduleSlug}")
            : url("/{$locale}/{$moduleSlug}");

        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Müzikler',
            'item' => $songsIndexUrl
        ];

        // 3. Genre (varsa)
        if ($this->genre) {
            $genreName = $this->genre->getTranslated('name', $locale);
            $genreSlug = $this->genre->getTranslated('slug', $locale);

            if ($genreName && $genreSlug) {
                $genreUrl = $locale === get_tenant_default_locale()
                    ? url("/genre/{$genreSlug}")
                    : url("/{$locale}/genre/{$genreSlug}");

                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $genreName,
                    'item' => $genreUrl
                ];
            }
        }

        // 4. Album (varsa)
        if ($this->album) {
            $albumTitle = $this->album->getTranslated('title', $locale);
            if ($albumTitle) {
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $albumTitle,
                    'item' => $this->album->getUrl($locale)
                ];
            }
        }

        // 5. Current Song
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
