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
use Modules\ReviewSystem\App\Traits\HasReviews;
use Modules\Muzibu\App\Traits\HasCachedCounts;

class Playlist extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, SoftDeletes, HasFavorites, HasReviews, Searchable, HasCachedCounts;

    protected $table = 'muzibu_playlists';
    protected $primaryKey = 'playlist_id';
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
        // NOT: title, slug, description CAST'LANMAMALI!
        // HasTranslations trait bunları otomatik yönetiyor
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
     * DB'de JSON constraint var, title/slug/description JSON formatında kayıtlı
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
     * Spatie Media Collections - hero tek dosya (yeni yüklenince eski silinir)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero')
            ->singleFile();
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
     * HasCachedCounts configuration
     * Defines cached count fields and their calculators
     */
    protected function getCachedCountsConfig(): array
    {
        return [
            'songs_count' => fn() => $this->songs()->where('muzibu_songs.is_active', true)->count(),
            'total_duration' => fn() => $this->songs()->where('muzibu_songs.is_active', true)->sum('duration'),
        ];
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
     * Total duration method (Radio model için)
     */
    public function getTotalDuration(): int
    {
        return $this->total_duration;
    }

    /**
     * Playlist'e şarkı ekle ve cache count'ları güncelle
     * @param int|Song $song Song ID veya Song instance
     * @param array $pivotData Pivot data (position vb.)
     */
    public function attachSongWithCache($song, array $pivotData = []): void
    {
        $songModel = $song instanceof Song ? $song : Song::find($song);

        if (!$songModel) {
            return;
        }

        // Zaten var mı kontrol et
        if ($this->songs()->where('muzibu_songs.song_id', $songModel->song_id)->exists()) {
            return;
        }

        // Şarkıyı ekle
        $this->songs()->attach($songModel->song_id, $pivotData);

        // Active şarkı ise cache'i güncelle
        if ($songModel->is_active) {
            $this->incrementCachedCount('songs_count');
            $this->incrementCachedCount('total_duration', (int) $songModel->duration);
        }
    }

    /**
     * Playlist'ten şarkı çıkar ve cache count'ları güncelle
     * @param int|Song $song Song ID veya Song instance
     */
    public function detachSongWithCache($song): void
    {
        $songModel = $song instanceof Song ? $song : Song::find($song);

        if (!$songModel) {
            return;
        }

        // Var mı kontrol et
        if (!$this->songs()->where('muzibu_songs.song_id', $songModel->song_id)->exists()) {
            return;
        }

        // Şarkıyı çıkar
        $this->songs()->detach($songModel->song_id);

        // Active şarkı ise cache'i güncelle
        if ($songModel->is_active) {
            $this->decrementCachedCount('songs_count');
            $this->decrementCachedCount('total_duration', (int) $songModel->duration);
        }
    }

    /**
     * Playlist şarkılarını senkronize et ve cache count'ları güncelle
     * @param array $songIds Şarkı ID'leri
     */
    public function syncSongsWithCache(array $songIds): void
    {
        // Şarkıları senkronize et
        $this->songs()->sync($songIds);

        // Cache'leri tamamen recalculate et
        $this->recalculateCachedCounts();
    }

    /**
     * Birden fazla şarkı ekle ve cache count'ları güncelle
     * @param array $songIds Şarkı ID'leri
     * @param array $pivotData Pivot data
     */
    public function attachManySongsWithCache(array $songIds, array $pivotData = []): void
    {
        if (empty($songIds)) {
            return;
        }

        // Mevcut şarkıları al
        $existingSongIds = $this->songs()->pluck('muzibu_songs.song_id')->toArray();
        $newSongIds = array_diff($songIds, $existingSongIds);

        if (empty($newSongIds)) {
            return;
        }

        // Yeni şarkıları ekle
        $attachData = [];
        foreach ($newSongIds as $songId) {
            $attachData[$songId] = $pivotData;
        }
        $this->songs()->attach($attachData);

        // Active şarkıların count ve duration'ını hesapla
        $addedSongs = Song::whereIn('song_id', $newSongIds)->where('is_active', true)->get();
        $addedCount = $addedSongs->count();
        $addedDuration = (int) $addedSongs->sum('duration');

        if ($addedCount > 0) {
            $this->incrementCachedCount('songs_count', $addedCount);
            $this->incrementCachedCount('total_duration', $addedDuration);
        }
    }

    /**
     * Birden fazla şarkı çıkar ve cache count'ları güncelle
     * @param array $songIds Şarkı ID'leri
     */
    public function detachManySongsWithCache(array $songIds): void
    {
        if (empty($songIds)) {
            return;
        }

        // Mevcut şarkıları al (çıkarılacak olanları)
        $existingSongIds = $this->songs()->whereIn('muzibu_songs.song_id', $songIds)->pluck('muzibu_songs.song_id')->toArray();

        if (empty($existingSongIds)) {
            return;
        }

        // Active şarkıların count ve duration'ını hesapla
        $removedSongs = Song::whereIn('song_id', $existingSongIds)->where('is_active', true)->get();
        $removedCount = $removedSongs->count();
        $removedDuration = (int) $removedSongs->sum('duration');

        // Şarkıları çıkar
        $this->songs()->detach($existingSongIds);

        if ($removedCount > 0) {
            $this->decrementCachedCount('songs_count', $removedCount);
            $this->decrementCachedCount('total_duration', $removedDuration);
        }
    }

    /**
     * Sektörler ilişkisi (Polymorphic - playlistables tablosu)
     * ✅ v5 sistemi: Artık playlistables tablosunu kullanıyor
     */
    public function sectors()
    {
        return $this->morphedByMany(
            Sector::class,
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Radyolar ilişkisi (Polymorphic - playlistables tablosu)
     * ✅ v5 sistemi: Artık playlistables tablosunu kullanıyor
     */
    public function radios()
    {
        return $this->morphedByMany(
            Radio::class,
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Türler ilişkisi (Polymorphic - playlistables tablosu)
     * ✅ v5 sistemi: Artık playlistables tablosunu kullanıyor
     */
    public function genres()
    {
        return $this->morphedByMany(
            Genre::class,
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Kurumsal hesaplar ilişkisi (Polymorphic - playlistables tablosu)
     * ✅ v5 sistemi: Corporate'lara playlist dağıtımı
     */
    public function corporates()
    {
        return $this->morphedByMany(
            MuzibuCorporateAccount::class,
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position')->withTimestamps();
    }

    // =========================================================================
    // POLYMORPHIC DISTRIBUTION RELATIONS (New v5 System)
    // =========================================================================

    /**
     * Playlist'in dağıtıldığı tüm entity'ler (sectors, radios, corporates, moods vb.)
     * Polymorphic many-to-many relation
     *
     * @param string|null $type Filter by type: 'sector', 'radio', 'corporate', 'mood'
     */
    public function distributedTo(?string $type = null)
    {
        $query = $this->morphedByMany(
            \Illuminate\Database\Eloquent\Model::class, // Generic, type ile belirlenir
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position', 'playlistable_type')->withTimestamps();

        if ($type) {
            $query->wherePivot('playlistable_type', $type);
        }

        return $query;
    }

    /**
     * Playlist'in dağıtıldığı sektörler (Polymorphic)
     */
    public function distributedToSectors()
    {
        return $this->morphedByMany(
            Sector::class,
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Playlist'in dağıtıldığı radyolar (Polymorphic)
     */
    public function distributedToRadios()
    {
        return $this->morphedByMany(
            Radio::class,
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Playlist'in dağıtıldığı türler (Polymorphic)
     */
    public function distributedToGenres()
    {
        return $this->morphedByMany(
            Genre::class,
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Playlist'in dağıtıldığı kurumsal hesaplar (Polymorphic)
     */
    public function distributedToCorporates()
    {
        return $this->morphedByMany(
            MuzibuCorporateAccount::class,
            'playlistable',
            'muzibu_playlistables',
            'playlist_id',
            'playlistable_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Tüm distribution entity'lerini tek sorguda getir
     * @return array ['sectors' => [...], 'radios' => [...], 'genres' => [...], 'corporates' => [...]]
     */
    public function getAllDistributions(): array
    {
        $distributions = \DB::table('muzibu_playlistables')
            ->where('playlist_id', $this->playlist_id)
            ->get()
            ->groupBy('playlistable_type');

        return [
            'sectors' => $distributions->get('sector', collect())->pluck('playlistable_id')->toArray(),
            'radios' => $distributions->get('radio', collect())->pluck('playlistable_id')->toArray(),
            'genres' => $distributions->get('genre', collect())->pluck('playlistable_id')->toArray(),
            'corporates' => $distributions->get('corporate', collect())->pluck('playlistable_id')->toArray(),
        ];
    }

    /**
     * Playlist'i bir entity'e dağıt
     *
     * @param string $type 'sector', 'radio', 'corporate', 'mood'
     * @param int $id Entity ID
     * @param int $position Sıralama
     */
    public function distributeToEntity(string $type, int $id, int $position = 0): void
    {
        \DB::table('muzibu_playlistables')->insertOrIgnore([
            'playlist_id' => $this->playlist_id,
            'playlistable_type' => $type,
            'playlistable_id' => $id,
            'position' => $position,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Playlist'i bir entity'den kaldır
     *
     * @param string $type 'sector', 'radio', 'corporate', 'mood'
     * @param int $id Entity ID
     */
    public function removeFromEntity(string $type, int $id): void
    {
        \DB::table('muzibu_playlistables')
            ->where('playlist_id', $this->playlist_id)
            ->where('playlistable_type', $type)
            ->where('playlistable_id', $id)
            ->delete();
    }

    /**
     * Playlist'in dağıtımlarını sync et
     *
     * @param string $type 'sector', 'radio', 'corporate', 'mood'
     * @param array $ids Entity ID'leri
     */
    public function syncDistribution(string $type, array $ids): void
    {
        // Önce bu type için tüm mevcut kayıtları sil
        \DB::table('muzibu_playlistables')
            ->where('playlist_id', $this->playlist_id)
            ->where('playlistable_type', $type)
            ->delete();

        // Yeni kayıtları ekle
        $records = [];
        foreach ($ids as $position => $id) {
            $records[] = [
                'playlist_id' => $this->playlist_id,
                'playlistable_type' => $type,
                'playlistable_id' => $id,
                'position' => $position,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($records)) {
            \DB::table('muzibu_playlistables')->insert($records);
        }
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
     * Sadece Spatie hero collection kullanır
     */
    public function getCoverUrl(?int $width = 600, ?int $height = 600, int $quality = 90): ?string
    {
        $heroMedia = $this->getFirstMedia('hero');
        return $heroMedia ? thumb($heroMedia, $width, $height, ['quality' => $quality, 'scale' => 1]) : null;
    }

    /**
     * Cover URL accessor (kare, thumbmaker ile)
     * Frontend için otomatik square cover (200x200, ortadan kırp)
     */
    public function getCoverUrlAttribute(): string
    {
        $heroMedia = $this->getFirstMedia('hero');
        return $heroMedia ? thumb($heroMedia, 200, 200, ['scale' => 1]) : '';
    }

    /**
     * Formatlanmış toplam süre (HH:MM:SS)
     */
    public function getFormattedTotalDuration(): string
    {
        $totalSeconds = $this->total_duration;

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Türkçe formatlanmış toplam süre (2s 45dk, 45dk 30sn, 3gün 2s 15dk)
     */
    public function getTurkishFormattedDuration(): string
    {
        $totalSeconds = $this->total_duration;

        if ($totalSeconds == 0) {
            return '0dk';
        }

        $days = floor($totalSeconds / 86400);
        $hours = floor(($totalSeconds % 86400) / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        $parts = [];

        if ($days > 0) {
            $parts[] = $days . 'gün';
        }
        if ($hours > 0) {
            $parts[] = $hours . 's';
        }
        if ($minutes > 0) {
            $parts[] = $minutes . 'dk';
        }
        // Sadece saniye varsa veya toplam 60 saniyeden azsa saniye göster
        if ($seconds > 0 && ($days == 0 && $hours == 0 && $minutes == 0)) {
            $parts[] = $seconds . 'sn';
        }

        return implode(' ', $parts);
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
        $songsCount = $this->songs_count;
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
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MusicPlaylist',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'image' => $this->getSeoFallbackImage(),
            'numTracks' => $this->songs_count,
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
     * Tüm schema'ları al (MusicPlaylist + Breadcrumb)
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. MusicPlaylist Schema
        $playlistSchema = $this->getSchemaMarkup();
        if ($playlistSchema) {
            $schemas['musicplaylist'] = $playlistSchema;
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
        $tenantId = tenant() ? tenant()->id : 1001;
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
