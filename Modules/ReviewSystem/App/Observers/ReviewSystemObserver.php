<?php

namespace Modules\ReviewSystem\App\Observers;

use Modules\ReviewSystem\App\Models\ReviewSystem;
use Modules\ReviewSystem\App\Exceptions\ReviewSystemValidationException;
use Modules\ReviewSystem\App\Exceptions\ReviewSystemProtectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * ReviewSystem Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class ReviewSystemObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the ReviewSystem "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(ReviewSystem $reviewsystem): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($reviewsystem->slug) && !empty($reviewsystem->title)) {
            $slugs = [];
            foreach ($reviewsystem->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $reviewsystem->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('reviewsystem.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($reviewsystem->$field)) {
                $reviewsystem->$field = $value;
            }
        }


        Log::info('ReviewSystem creating', [
            'title' => $reviewsystem->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the ReviewSystem "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(ReviewSystem $reviewsystem): void
    {
        // Cache temizle
        $this->clearReviewSystemCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($reviewsystem, 'oluşturuldu');
        }

        Log::info('ReviewSystem created successfully', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'title' => $reviewsystem->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the ReviewSystem "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(ReviewSystem $reviewsystem): void
    {
        // Değişen alanları tespit et
        $dirty = $reviewsystem->getDirty();



        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $reviewsystem->reviewsystem_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $reviewsystem->reviewsystem_id);
                    }
                }
                $reviewsystem->slug = $dirty['slug'];
            }
        }

        Log::info('ReviewSystem updating', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the ReviewSystem "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(ReviewSystem $reviewsystem): void
    {
        // Cache temizle
        $this->clearReviewSystemCaches($reviewsystem->reviewsystem_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $reviewsystem->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                log_activity($reviewsystem, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('ReviewSystem updated successfully', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the ReviewSystem "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(ReviewSystem $reviewsystem): void
    {
        // Title ve slug validasyon
        if (is_array($reviewsystem->title)) {
            foreach ($reviewsystem->title as $locale => $title) {
                $minLength = config('reviewsystem.validation.title.min', 3);
                $maxLength = config('reviewsystem.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw ReviewSystemValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $reviewsystem->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('ReviewSystem title auto-trimmed', [
                            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
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
     * Handle the ReviewSystem "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(ReviewSystem $reviewsystem): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_reviewsystem_{$reviewsystem->reviewsystem_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('reviewsystem.show', $reviewsystem->slug));
        }
    }

    /**
     * Handle the ReviewSystem "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(ReviewSystem $reviewsystem): bool
    {
        // Reserved slug kontrolü
        $reservedSlugs = config('reviewsystem.slug.reserved_slugs', []);
        if (is_array($reviewsystem->slug)) {
            foreach ($reviewsystem->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw ReviewSystemProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('ReviewSystem deleting', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'title' => $reviewsystem->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the ReviewSystem "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(ReviewSystem $reviewsystem): void
    {
        // Spatie Media Library - Görselleri temizle
        $reviewsystem->clearMediaCollection('featured_image');
        $reviewsystem->clearMediaCollection('gallery');

        // Cache temizle
        $this->clearReviewSystemCaches($reviewsystem->reviewsystem_id);

        // SEO ayarlarını da sil
        if ($reviewsystem->seoSetting) {
            $reviewsystem->seoSetting->delete();
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($reviewsystem, 'silindi');
        }

        Log::info('ReviewSystem deleted successfully', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'title' => $reviewsystem->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the ReviewSystem "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(ReviewSystem $reviewsystem): void
    {
        Log::info('ReviewSystem restoring', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'title' => $reviewsystem->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the ReviewSystem "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(ReviewSystem $reviewsystem): void
    {
        // Cache temizle
        $this->clearReviewSystemCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($reviewsystem, 'geri yüklendi');
        }

        Log::info('ReviewSystem restored successfully', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'title' => $reviewsystem->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the ReviewSystem "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(ReviewSystem $reviewsystem): bool
    {
        Log::warning('ReviewSystem force deleting', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'title' => $reviewsystem->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the ReviewSystem "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(ReviewSystem $reviewsystem): void
    {
        // Spatie Media Library - Görselleri temizle
        $reviewsystem->clearMediaCollection('featured_image');
        $reviewsystem->clearMediaCollection('gallery');

        // Tüm cache'leri temizle
        $this->clearReviewSystemCaches($reviewsystem->reviewsystem_id);

        Log::warning('ReviewSystem force deleted', [
            'reviewsystem_id' => $reviewsystem->reviewsystem_id,
            'title' => $reviewsystem->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * ReviewSystem cache'lerini temizle
     */
    private function clearReviewSystemCaches(?int $reviewsystemId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('reviewsystems');

        // Spesifik cache key'leri temizle
        Cache::forget('reviewsystems_list');
        Cache::forget('reviewsystems_menu_cache');
        Cache::forget('reviewsystems_sitemap_cache');

        if ($reviewsystemId) {
            Cache::forget("reviewsystem_detail_{$reviewsystemId}");
            Cache::forget("universal_seo_reviewsystem_{$reviewsystemId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['reviewsystems', 'content'])->flush();
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
        $query = ReviewSystem::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('reviewsystem_id', '!=', $excludeId);
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
