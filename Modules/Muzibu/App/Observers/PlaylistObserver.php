<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Playlist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Playlist Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class PlaylistObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Playlist "creating" event.
     */
    public function creating(Playlist $playlist): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($playlist->slug) && !empty($playlist->title)) {
            $slugs = [];
            foreach ($playlist->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $playlist->slug = $slugs;
            }
        }

        // Varsayılan değerleri ayarla
        if (!isset($playlist->is_active)) {
            $playlist->is_active = true;
        }

        if (!isset($playlist->is_public)) {
            $playlist->is_public = false;
        }

        if (!isset($playlist->is_system)) {
            $playlist->is_system = false;
        }

        Log::info('Muzibu Playlist creating', [
            'title' => $playlist->title,
            'user_id' => auth()->id(),
            'is_system' => $playlist->is_system
        ]);
    }

    /**
     * Handle the Playlist "created" event.
     */
    public function created(Playlist $playlist): void
    {
        $this->clearPlaylistCaches();

        if (function_exists('log_activity')) {
            log_activity($playlist, 'oluşturuldu');
        }

        Log::info('Muzibu Playlist created successfully', [
            'playlist_id' => $playlist->playlist_id,
            'title' => $playlist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Playlist "updating" event.
     */
    public function updating(Playlist $playlist): void
    {
        $dirty = $playlist->getDirty();

        if (isset($dirty['slug']) && is_array($dirty['slug'])) {
            foreach ($dirty['slug'] as $locale => $slug) {
                if ($this->isSlugTaken($slug, $locale, $playlist->playlist_id)) {
                    $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $playlist->playlist_id);
                }
            }
            $playlist->slug = $dirty['slug'];
        }

        Log::info('Muzibu Playlist updating', [
            'playlist_id' => $playlist->playlist_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Playlist "updated" event.
     */
    public function updated(Playlist $playlist): void
    {
        $this->clearPlaylistCaches($playlist->playlist_id);

        if (function_exists('log_activity')) {
            $changes = $playlist->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $playlist->getOriginal('title');
                }

                log_activity($playlist, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Muzibu Playlist updated successfully', [
            'playlist_id' => $playlist->playlist_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Playlist "saving" event.
     */
    public function saving(Playlist $playlist): void
    {
        if (is_array($playlist->title)) {
            foreach ($playlist->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Playlist adı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    if (strlen($title) > $maxLength) {
                        $playlist->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Muzibu Playlist title auto-trimmed', [
                            'playlist_id' => $playlist->playlist_id,
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
     * Handle the Playlist "saved" event.
     */
    public function saved(Playlist $playlist): void
    {
        Cache::forget("universal_seo_muzibu_playlist_{$playlist->playlist_id}");

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the Playlist "deleting" event.
     */
    public function deleting(Playlist $playlist): bool
    {
        Log::info('Muzibu Playlist deleting', [
            'playlist_id' => $playlist->playlist_id,
            'title' => $playlist->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Playlist "deleted" event.
     */
    public function deleted(Playlist $playlist): void
    {
        $this->clearPlaylistCaches($playlist->playlist_id);

        // Playlist-Song ilişkilerini temizle
        $playlist->songs()->detach();

        if ($playlist->seoSetting) {
            $playlist->seoSetting->delete();
        }

        if (function_exists('log_activity')) {
            log_activity($playlist, 'silindi', null, $playlist->title);
        }

        Log::info('Muzibu Playlist deleted successfully', [
            'playlist_id' => $playlist->playlist_id,
            'title' => $playlist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Playlist "restoring" event.
     */
    public function restoring(Playlist $playlist): void
    {
        Log::info('Muzibu Playlist restoring', [
            'playlist_id' => $playlist->playlist_id,
            'title' => $playlist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Playlist "restored" event.
     */
    public function restored(Playlist $playlist): void
    {
        $this->clearPlaylistCaches();

        if (function_exists('log_activity')) {
            log_activity($playlist, 'geri yüklendi');
        }

        Log::info('Muzibu Playlist restored successfully', [
            'playlist_id' => $playlist->playlist_id,
            'title' => $playlist->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Playlist "forceDeleting" event.
     */
    public function forceDeleting(Playlist $playlist): bool
    {
        Log::warning('Muzibu Playlist force deleting', [
            'playlist_id' => $playlist->playlist_id,
            'title' => $playlist->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Playlist "forceDeleted" event.
     */
    public function forceDeleted(Playlist $playlist): void
    {
        $this->clearPlaylistCaches($playlist->playlist_id);

        if (function_exists('log_activity')) {
            log_activity($playlist, 'kalıcı silindi', null, $playlist->title);
        }

        Log::warning('Muzibu Playlist force deleted', [
            'playlist_id' => $playlist->playlist_id,
            'title' => $playlist->title,
            'user_id' => auth()->id()
        ]);
    }

    private function clearPlaylistCaches(?int $playlistId = null): void
    {
        $this->cacheService->flushByPrefix('muzibu_playlists');

        Cache::forget('muzibu_playlists_list');
        Cache::forget('muzibu_playlists_active');
        Cache::forget('muzibu_playlists_system');
        Cache::forget('muzibu_playlists_public');

        if ($playlistId) {
            Cache::forget("muzibu_playlist_detail_{$playlistId}");
            Cache::forget("universal_seo_muzibu_playlist_{$playlistId}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_playlists', 'muzibu', 'content'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    private function isSlugTaken(string $slug, string $locale, ?int $excludeId = null): bool
    {
        $query = Playlist::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('playlist_id', '!=', $excludeId);
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
