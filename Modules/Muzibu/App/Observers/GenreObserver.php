<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Genre;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Genre Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class GenreObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Genre "creating" event.
     */
    public function creating(Genre $genre): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($genre->slug) && !empty($genre->title)) {
            $slugs = [];
            foreach ($genre->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $genre->slug = $slugs;
            }
        }

        // Varsayılan değerleri ayarla
        if (!isset($genre->is_active)) {
            $genre->is_active = true;
        }

        Log::info('Muzibu Genre creating', [
            'title' => $genre->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Genre "created" event.
     */
    public function created(Genre $genre): void
    {
        $this->clearGenreCaches();

        if (function_exists('log_activity')) {
            log_activity($genre, 'oluşturuldu');
        }

        Log::info('Muzibu Genre created successfully', [
            'genre_id' => $genre->genre_id,
            'title' => $genre->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Genre "updating" event.
     */
    public function updating(Genre $genre): void
    {
        $dirty = $genre->getDirty();

        if (isset($dirty['slug']) && is_array($dirty['slug'])) {
            foreach ($dirty['slug'] as $locale => $slug) {
                if ($this->isSlugTaken($slug, $locale, $genre->genre_id)) {
                    $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $genre->genre_id);
                }
            }
            $genre->slug = $dirty['slug'];
        }

        Log::info('Muzibu Genre updating', [
            'genre_id' => $genre->genre_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Genre "updated" event.
     */
    public function updated(Genre $genre): void
    {
        $this->clearGenreCaches($genre->genre_id);

        if (function_exists('log_activity')) {
            $changes = $genre->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $genre->getOriginal('title');
                }

                log_activity($genre, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Muzibu Genre updated successfully', [
            'genre_id' => $genre->genre_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Genre "saving" event.
     */
    public function saving(Genre $genre): void
    {
        if (is_array($genre->title)) {
            foreach ($genre->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Tür adı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    if (strlen($title) > $maxLength) {
                        $genre->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Muzibu Genre title auto-trimmed', [
                            'genre_id' => $genre->genre_id,
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
     * Handle the Genre "saved" event.
     */
    public function saved(Genre $genre): void
    {
        Cache::forget("universal_seo_muzibu_genre_{$genre->genre_id}");

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the Genre "deleting" event.
     */
    public function deleting(Genre $genre): bool
    {
        if ($genre->songs()->count() > 0) {
            throw new \Exception('Bu türe ait şarkılar var. Önce şarkıları başka türe taşımalısınız.');
        }

        Log::info('Muzibu Genre deleting', [
            'genre_id' => $genre->genre_id,
            'title' => $genre->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Genre "deleted" event.
     */
    public function deleted(Genre $genre): void
    {
        $this->clearGenreCaches($genre->genre_id);

        if ($genre->seoSetting) {
            $genre->seoSetting->delete();
        }

        if (function_exists('log_activity')) {
            log_activity($genre, 'silindi', null, $genre->title);
        }

        Log::info('Muzibu Genre deleted successfully', [
            'genre_id' => $genre->genre_id,
            'title' => $genre->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Genre "restoring" event.
     */
    public function restoring(Genre $genre): void
    {
        Log::info('Muzibu Genre restoring', [
            'genre_id' => $genre->genre_id,
            'title' => $genre->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Genre "restored" event.
     */
    public function restored(Genre $genre): void
    {
        $this->clearGenreCaches();

        if (function_exists('log_activity')) {
            log_activity($genre, 'geri yüklendi');
        }

        Log::info('Muzibu Genre restored successfully', [
            'genre_id' => $genre->genre_id,
            'title' => $genre->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Genre "forceDeleting" event.
     */
    public function forceDeleting(Genre $genre): bool
    {
        Log::warning('Muzibu Genre force deleting', [
            'genre_id' => $genre->genre_id,
            'title' => $genre->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Genre "forceDeleted" event.
     */
    public function forceDeleted(Genre $genre): void
    {
        $this->clearGenreCaches($genre->genre_id);

        if (function_exists('log_activity')) {
            log_activity($genre, 'kalıcı silindi', null, $genre->title);
        }

        Log::warning('Muzibu Genre force deleted', [
            'genre_id' => $genre->genre_id,
            'title' => $genre->title,
            'user_id' => auth()->id()
        ]);
    }

    private function clearGenreCaches(?int $genreId = null): void
    {
        $this->cacheService->flushByPrefix('muzibu_genres');

        Cache::forget('muzibu_genres_list');
        Cache::forget('muzibu_genres_active');

        if ($genreId) {
            Cache::forget("muzibu_genre_detail_{$genreId}");
            Cache::forget("universal_seo_muzibu_genre_{$genreId}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_genres', 'muzibu', 'content'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    private function isSlugTaken(string $slug, string $locale, ?int $excludeId = null): bool
    {
        $query = Genre::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('genre_id', '!=', $excludeId);
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
