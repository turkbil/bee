<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Muzibu;
use Modules\Muzibu\App\Exceptions\MuzibuValidationException;
use Modules\Muzibu\App\Exceptions\MuzibuProtectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Muzibu Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class MuzibuObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Muzibu "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Muzibu $muzibu): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($muzibu->slug) && !empty($muzibu->title)) {
            $slugs = [];
            foreach ($muzibu->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $muzibu->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('muzibu.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($muzibu->$field)) {
                $muzibu->$field = $value;
            }
        }


        Log::info('Muzibu creating', [
            'title' => $muzibu->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Muzibu "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Muzibu $muzibu): void
    {
        // Cache temizle
        $this->clearMuzibuCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($muzibu, 'oluşturuldu');
        }

        Log::info('Muzibu created successfully', [
            'muzibu_id' => $muzibu->muzibu_id,
            'title' => $muzibu->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Muzibu "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Muzibu $muzibu): void
    {
        // Değişen alanları tespit et
        $dirty = $muzibu->getDirty();



        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $muzibu->muzibu_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $muzibu->muzibu_id);
                    }
                }
                $muzibu->slug = $dirty['slug'];
            }
        }

        Log::info('Muzibu updating', [
            'muzibu_id' => $muzibu->muzibu_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Muzibu "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Muzibu $muzibu): void
    {
        // Cache temizle
        $this->clearMuzibuCaches($muzibu->muzibu_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $muzibu->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                // Eski başlığı al (title değiştiyse)
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $muzibu->getOriginal('title');
                }

                log_activity($muzibu, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Muzibu updated successfully', [
            'muzibu_id' => $muzibu->muzibu_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Muzibu "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Muzibu $muzibu): void
    {
        // Title ve slug validasyon
        if (is_array($muzibu->title)) {
            foreach ($muzibu->title as $locale => $title) {
                $minLength = config('muzibu.validation.title.min', 3);
                $maxLength = config('muzibu.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw MuzibuValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $muzibu->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Muzibu title auto-trimmed', [
                            'muzibu_id' => $muzibu->muzibu_id,
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
     * Handle the Muzibu "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Muzibu $muzibu): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_muzibu_{$muzibu->muzibu_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('muzibu.show', $muzibu->slug));
        }
    }

    /**
     * Handle the Muzibu "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Muzibu $muzibu): bool
    {
        // Reserved slug kontrolü
        $reservedSlugs = config('muzibu.slug.reserved_slugs', []);
        if (is_array($muzibu->slug)) {
            foreach ($muzibu->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw MuzibuProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('Muzibu deleting', [
            'muzibu_id' => $muzibu->muzibu_id,
            'title' => $muzibu->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Muzibu "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Muzibu $muzibu): void
    {
        // Spatie Media Library - Görselleri temizle
        $muzibu->clearMediaCollection('featured_image');
        $muzibu->clearMediaCollection('gallery');

        // Cache temizle
        $this->clearMuzibuCaches($muzibu->muzibu_id);

        // SEO ayarlarını da sil
        if ($muzibu->seoSetting) {
            $muzibu->seoSetting->delete();
        }

        // Activity log - silinen kaydın başlığını sakla
        if (function_exists('log_activity')) {
            log_activity($muzibu, 'silindi', null, $muzibu->title);
        }

        Log::info('Muzibu deleted successfully', [
            'muzibu_id' => $muzibu->muzibu_id,
            'title' => $muzibu->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Muzibu "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Muzibu $muzibu): void
    {
        Log::info('Muzibu restoring', [
            'muzibu_id' => $muzibu->muzibu_id,
            'title' => $muzibu->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Muzibu "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Muzibu $muzibu): void
    {
        // Cache temizle
        $this->clearMuzibuCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($muzibu, 'geri yüklendi');
        }

        Log::info('Muzibu restored successfully', [
            'muzibu_id' => $muzibu->muzibu_id,
            'title' => $muzibu->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Muzibu "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Muzibu $muzibu): bool
    {
        Log::warning('Muzibu force deleting', [
            'muzibu_id' => $muzibu->muzibu_id,
            'title' => $muzibu->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Muzibu "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Muzibu $muzibu): void
    {
        // Spatie Media Library - Görselleri temizle
        $muzibu->clearMediaCollection('featured_image');
        $muzibu->clearMediaCollection('gallery');

        // Tüm cache'leri temizle
        $this->clearMuzibuCaches($muzibu->muzibu_id);

        // Activity log - kalıcı silme
        if (function_exists('log_activity')) {
            log_activity($muzibu, 'kalıcı silindi', null, $muzibu->title);
        }

        Log::warning('Muzibu force deleted', [
            'muzibu_id' => $muzibu->muzibu_id,
            'title' => $muzibu->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Muzibu cache'lerini temizle
     */
    private function clearMuzibuCaches(?int $muzibuId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('muzibus');

        // Spesifik cache key'leri temizle
        Cache::forget('muzibus_list');
        Cache::forget('muzibus_menu_cache');
        Cache::forget('muzibus_sitemap_cache');

        if ($muzibuId) {
            Cache::forget("muzibu_detail_{$muzibuId}");
            Cache::forget("universal_seo_muzibu_{$muzibuId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibus', 'content'])->flush();
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
        $query = Muzibu::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('muzibu_id', '!=', $excludeId);
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
