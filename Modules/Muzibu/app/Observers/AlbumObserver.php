<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Artist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Album Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class AlbumObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Album "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Album $album): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($album->slug) && !empty($album->title)) {
            $slugs = [];
            foreach ($album->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $album->slug = $slugs;
            }
        }

        // Varsayılan değerleri ayarla
        if (!isset($album->is_active)) {
            $album->is_active = true;
        }

        Log::info('Muzibu Album creating', [
            'title' => $album->title,
            'artist_id' => $album->artist_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Album "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Album $album): void
    {
        // Cache temizle
        $this->clearAlbumCaches();

        // Update Artist albums_count
        if ($album->is_active && $album->artist_id) {
            $artist = Artist::find($album->artist_id);
            if ($artist) {
                $artist->incrementCachedCount('albums_count');
            }
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($album, 'oluşturuldu');
        }

        Log::info('Muzibu Album created successfully', [
            'album_id' => $album->album_id,
            'title' => $album->title,
            'artist_id' => $album->artist_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Album "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Album $album): void
    {
        // Değişen alanları tespit et
        $dirty = $album->getDirty();

        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $album->album_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $album->album_id);
                    }
                }
                $album->slug = $dirty['slug'];
            }
        }

        Log::info('Muzibu Album updating', [
            'album_id' => $album->album_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Album "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Album $album): void
    {
        // Cache temizle
        $this->clearAlbumCaches($album->album_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $album->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                log_activity($album, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('Muzibu Album updated successfully', [
            'album_id' => $album->album_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Album "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Album $album): void
    {
        // Title validasyon
        if (is_array($album->title)) {
            foreach ($album->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Albüm başlığı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    // Maximum length check - auto trim
                    if (strlen($title) > $maxLength) {
                        $album->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Muzibu Album title auto-trimmed', [
                            'album_id' => $album->album_id,
                            'locale' => $locale,
                            'original_length' => strlen($title),
                            'trimmed_length' => $maxLength
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Handle the Album "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Album $album): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_muzibu_album_{$album->album_id}");

        // Response cache temizle - tüm album URL'lerini temizle
        $this->clearAlbumResponseCache($album);
    }

    /**
     * Handle the Album "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Album $album): bool
    {
        // Albüme bağlı şarkılar varsa silme
        if ($album->songs()->count() > 0) {
            throw new \Exception('Bu albüme ait şarkılar var. Önce şarkıları silmelisiniz veya başka albüme taşımalısınız.');
        }

        Log::info('Muzibu Album deleting', [
            'album_id' => $album->album_id,
            'title' => $album->title,
            'artist_id' => $album->artist_id,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Album "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Album $album): void
    {
        // Cache temizle
        $this->clearAlbumCaches($album->album_id);

        // Update Artist albums_count
        if ($album->is_active && $album->artist_id) {
            $artist = Artist::find($album->artist_id);
            if ($artist) {
                $artist->decrementCachedCount('albums_count');
            }
        }

        // SEO ayarlarını da sil
        if ($album->seoSetting) {
            $album->seoSetting->delete();
        }

        // Media temizleme (Spatie Media Library otomatik siler, ama ek kontrol)
        if ($album->media_id && $album->coverMedia) {
            Log::info('Muzibu Album cover media will be auto-deleted by Spatie', [
                'album_id' => $album->album_id,
                'media_id' => $album->media_id
            ]);
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($album, 'silindi');
        }

        Log::info('Muzibu Album deleted successfully', [
            'album_id' => $album->album_id,
            'title' => $album->title,
            'artist_id' => $album->artist_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Album "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Album $album): void
    {
        Log::info('Muzibu Album restoring', [
            'album_id' => $album->album_id,
            'title' => $album->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Album "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Album $album): void
    {
        // Cache temizle
        $this->clearAlbumCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($album, 'geri yüklendi');
        }

        Log::info('Muzibu Album restored successfully', [
            'album_id' => $album->album_id,
            'title' => $album->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Album "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Album $album): bool
    {
        Log::warning('Muzibu Album force deleting', [
            'album_id' => $album->album_id,
            'title' => $album->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Album "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Album $album): void
    {
        // Tüm cache'leri temizle
        $this->clearAlbumCaches($album->album_id);

        Log::warning('Muzibu Album force deleted', [
            'album_id' => $album->album_id,
            'title' => $album->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Album cache'lerini temizle
     */
    private function clearAlbumCaches(?int $albumId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('muzibu_albums');

        // Spesifik cache key'leri temizle
        Cache::forget('muzibu_albums_list');
        Cache::forget('muzibu_albums_menu_cache');
        Cache::forget('muzibu_albums_active');

        if ($albumId) {
            Cache::forget("muzibu_album_detail_{$albumId}");
            Cache::forget("universal_seo_muzibu_album_{$albumId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_albums', 'muzibu', 'content'])->flush();
        }

        // Response cache temizle - FULL CLEAR
        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Album response cache'lerini temizle (URL bazlı)
     */
    private function clearAlbumResponseCache(Album $album): void
    {
        if (!class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            return;
        }

        // Tüm locale slug'ları al
        $slugs = is_array($album->slug) ? $album->slug : ['tr' => $album->slug];

        foreach ($slugs as $locale => $slug) {
            if (empty($slug)) {
                continue;
            }

            try {
                // Web route (themes/muzibu)
                $webUrl = route('muzibu.albums.show', ['slug' => $slug], false);
                \Spatie\ResponseCache\Facades\ResponseCache::forget($webUrl);

                // API route (SPA)
                $apiUrl = route('muzibu.api.albums.show', ['slug' => $slug], false);
                \Spatie\ResponseCache\Facades\ResponseCache::forget($apiUrl);

                Log::debug('Album response cache cleared', [
                    'album_id' => $album->album_id,
                    'slug' => $slug,
                    'locale' => $locale,
                    'urls' => [$webUrl, $apiUrl]
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to clear album response cache', [
                    'album_id' => $album->album_id,
                    'slug' => $slug,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Güvenlik için full clear de yap
        \Spatie\ResponseCache\Facades\ResponseCache::clear();
    }

    /**
     * Slug'ın benzersiz olup olmadığını kontrol et
     */
    private function isSlugTaken(string $slug, string $locale, ?int $excludeId = null): bool
    {
        $query = Album::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('album_id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Benzersiz slug oluştur
     */
    private function generateUniqueSlug(string $baseSlug, string $locale, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->isSlugTaken($slug, $locale, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
