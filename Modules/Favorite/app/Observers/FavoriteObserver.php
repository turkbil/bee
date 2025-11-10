<?php

namespace Modules\Favorite\App\Observers;

use Modules\Favorite\App\Models\Favorite;
use Modules\Favorite\App\Exceptions\FavoriteValidationException;
use Modules\Favorite\App\Exceptions\FavoriteProtectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Favorite Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class FavoriteObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Favorite "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Favorite $favorite): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($favorite->slug) && !empty($favorite->title)) {
            $slugs = [];
            foreach ($favorite->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $favorite->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('favorite.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($favorite->$field)) {
                $favorite->$field = $value;
            }
        }


        Log::info('Favorite creating', [
            'title' => $favorite->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Favorite "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Favorite $favorite): void
    {
        // Cache temizle
        $this->clearFavoriteCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($favorite, 'oluşturuldu');
        }

        Log::info('Favorite created successfully', [
            'favorite_id' => $favorite->favorite_id,
            'title' => $favorite->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Favorite "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Favorite $favorite): void
    {
        // Değişen alanları tespit et
        $dirty = $favorite->getDirty();



        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $favorite->favorite_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $favorite->favorite_id);
                    }
                }
                $favorite->slug = $dirty['slug'];
            }
        }

        Log::info('Favorite updating', [
            'favorite_id' => $favorite->favorite_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Favorite "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Favorite $favorite): void
    {
        // Cache temizle
        $this->clearFavoriteCaches($favorite->favorite_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $favorite->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                log_activity($favorite, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('Favorite updated successfully', [
            'favorite_id' => $favorite->favorite_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Favorite "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Favorite $favorite): void
    {
        // Title ve slug validasyon
        if (is_array($favorite->title)) {
            foreach ($favorite->title as $locale => $title) {
                $minLength = config('favorite.validation.title.min', 3);
                $maxLength = config('favorite.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw FavoriteValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $favorite->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Favorite title auto-trimmed', [
                            'favorite_id' => $favorite->favorite_id,
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
     * Handle the Favorite "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Favorite $favorite): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_favorite_{$favorite->favorite_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('favorite.show', $favorite->slug));
        }
    }

    /**
     * Handle the Favorite "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Favorite $favorite): bool
    {
        // Reserved slug kontrolü
        $reservedSlugs = config('favorite.slug.reserved_slugs', []);
        if (is_array($favorite->slug)) {
            foreach ($favorite->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw FavoriteProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('Favorite deleting', [
            'favorite_id' => $favorite->favorite_id,
            'title' => $favorite->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Favorite "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Favorite $favorite): void
    {
        // Spatie Media Library - Görselleri temizle
        $favorite->clearMediaCollection('featured_image');
        $favorite->clearMediaCollection('gallery');

        // Cache temizle
        $this->clearFavoriteCaches($favorite->favorite_id);

        // SEO ayarlarını da sil
        if ($favorite->seoSetting) {
            $favorite->seoSetting->delete();
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($favorite, 'silindi');
        }

        Log::info('Favorite deleted successfully', [
            'favorite_id' => $favorite->favorite_id,
            'title' => $favorite->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Favorite "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Favorite $favorite): void
    {
        Log::info('Favorite restoring', [
            'favorite_id' => $favorite->favorite_id,
            'title' => $favorite->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Favorite "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Favorite $favorite): void
    {
        // Cache temizle
        $this->clearFavoriteCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($favorite, 'geri yüklendi');
        }

        Log::info('Favorite restored successfully', [
            'favorite_id' => $favorite->favorite_id,
            'title' => $favorite->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Favorite "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Favorite $favorite): bool
    {
        Log::warning('Favorite force deleting', [
            'favorite_id' => $favorite->favorite_id,
            'title' => $favorite->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Favorite "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Favorite $favorite): void
    {
        // Spatie Media Library - Görselleri temizle
        $favorite->clearMediaCollection('featured_image');
        $favorite->clearMediaCollection('gallery');

        // Tüm cache'leri temizle
        $this->clearFavoriteCaches($favorite->favorite_id);

        Log::warning('Favorite force deleted', [
            'favorite_id' => $favorite->favorite_id,
            'title' => $favorite->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Favorite cache'lerini temizle
     */
    private function clearFavoriteCaches(?int $favoriteId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('favorites');

        // Spesifik cache key'leri temizle
        Cache::forget('favorites_list');
        Cache::forget('favorites_menu_cache');
        Cache::forget('favorites_sitemap_cache');

        if ($favoriteId) {
            Cache::forget("favorite_detail_{$favoriteId}");
            Cache::forget("universal_seo_favorite_{$favoriteId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['favorites', 'content'])->flush();
        }

        // Response cache temizle
        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Slug'ın benzersiz olup olmadığını kontrol et
     */
    private function isSlugTaken(string $slug, string $locale, ?int $excludeId = null): bool
    {
        $query = Favorite::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('favorite_id', '!=', $excludeId);
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
