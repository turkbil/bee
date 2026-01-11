<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Artist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Artist Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class ArtistObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Artist "creating" event.
     */
    public function creating(Artist $artist): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($artist->slug) && !empty($artist->title)) {
            $slugs = [];
            foreach ($artist->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $artist->slug = $slugs;
            }
        }

        // Varsayılan değerleri ayarla
        if (!isset($artist->is_active)) {
            $artist->is_active = true;
        }

        Log::info('Muzibu Artist creating', [
            'title' => $artist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Artist "created" event.
     */
    public function created(Artist $artist): void
    {
        // Cache temizle
        $this->clearArtistCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($artist, 'oluşturuldu');
        }

        Log::info('Muzibu Artist created successfully', [
            'artist_id' => $artist->artist_id,
            'title' => $artist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Artist "updating" event.
     */
    public function updating(Artist $artist): void
    {
        $dirty = $artist->getDirty();

        // Slug benzersizlik kontrolü
        if (isset($dirty['slug']) && is_array($dirty['slug'])) {
            foreach ($dirty['slug'] as $locale => $slug) {
                if ($this->isSlugTaken($slug, $locale, $artist->artist_id)) {
                    $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $artist->artist_id);
                }
            }
            $artist->slug = $dirty['slug'];
        }

        Log::info('Muzibu Artist updating', [
            'artist_id' => $artist->artist_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Artist "updated" event.
     */
    public function updated(Artist $artist): void
    {
        // Cache temizle
        $this->clearArtistCaches($artist->artist_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $artist->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                // Eski başlığı al (title değiştiyse)
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $artist->getOriginal('title');
                }

                log_activity($artist, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Muzibu Artist updated successfully', [
            'artist_id' => $artist->artist_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Artist "saving" event.
     */
    public function saving(Artist $artist): void
    {
        // Title validasyon
        if (is_array($artist->title)) {
            foreach ($artist->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Sanatçı adı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    if (strlen($title) > $maxLength) {
                        $artist->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Muzibu Artist title auto-trimmed', [
                            'artist_id' => $artist->artist_id,
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
     * Handle the Artist "saved" event.
     */
    public function saved(Artist $artist): void
    {
        Cache::forget("universal_seo_muzibu_artist_{$artist->artist_id}");

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the Artist "deleting" event.
     */
    public function deleting(Artist $artist): bool
    {
        // Sanatçıya bağlı albümler varsa silme
        if ($artist->albums()->count() > 0) {
            throw new \Exception('Bu sanatçıya ait albümler var. Önce albümleri silmelisiniz.');
        }

        Log::info('Muzibu Artist deleting', [
            'artist_id' => $artist->artist_id,
            'title' => $artist->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Artist "deleted" event.
     */
    public function deleted(Artist $artist): void
    {
        $this->clearArtistCaches($artist->artist_id);

        if ($artist->seoSetting) {
            $artist->seoSetting->delete();
        }

        // Activity log - silinen kaydın başlığını sakla
        if (function_exists('log_activity')) {
            log_activity($artist, 'silindi', null, $artist->title);
        }

        Log::info('Muzibu Artist deleted successfully', [
            'artist_id' => $artist->artist_id,
            'title' => $artist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Artist "restoring" event.
     */
    public function restoring(Artist $artist): void
    {
        Log::info('Muzibu Artist restoring', [
            'artist_id' => $artist->artist_id,
            'title' => $artist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Artist "restored" event.
     */
    public function restored(Artist $artist): void
    {
        $this->clearArtistCaches();

        if (function_exists('log_activity')) {
            log_activity($artist, 'geri yüklendi');
        }

        Log::info('Muzibu Artist restored successfully', [
            'artist_id' => $artist->artist_id,
            'title' => $artist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Artist "forceDeleting" event.
     */
    public function forceDeleting(Artist $artist): bool
    {
        Log::warning('Muzibu Artist force deleting', [
            'artist_id' => $artist->artist_id,
            'title' => $artist->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Artist "forceDeleted" event.
     */
    public function forceDeleted(Artist $artist): void
    {
        $this->clearArtistCaches($artist->artist_id);

        if (function_exists('log_activity')) {
            log_activity($artist, 'kalıcı silindi', null, $artist->title);
        }

        Log::warning('Muzibu Artist force deleted', [
            'artist_id' => $artist->artist_id,
            'title' => $artist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Artist cache'lerini temizle
     */
    private function clearArtistCaches(?int $artistId = null): void
    {
        $this->cacheService->flushByPrefix('muzibu_artists');

        Cache::forget('muzibu_artists_list');
        Cache::forget('muzibu_artists_active');

        if ($artistId) {
            Cache::forget("muzibu_artist_detail_{$artistId}");
            Cache::forget("universal_seo_muzibu_artist_{$artistId}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_artists', 'muzibu', 'content'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    private function isSlugTaken(string $slug, string $locale, ?int $excludeId = null): bool
    {
        $query = Artist::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('artist_id', '!=', $excludeId);
        }

        return $query->exists();
    }

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
