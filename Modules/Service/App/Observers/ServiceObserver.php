<?php

namespace Modules\Service\App\Observers;

use Modules\Service\App\Models\Service;
use Modules\Service\App\Exceptions\ServiceValidationException;
use Modules\Service\App\Exceptions\ServiceProtectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Service Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class ServiceObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Service "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Service $service): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($service->slug) && !empty($service->title)) {
            $slugs = [];
            foreach ($service->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $service->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('service.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($service->$field)) {
                $service->$field = $value;
            }
        }


        Log::info('Service creating', [
            'title' => $service->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Service "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Service $service): void
    {
        // Cache temizle
        $this->clearServiceCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($service, 'oluşturuldu');
        }

        Log::info('Service created successfully', [
            'service_id' => $service->service_id,
            'title' => $service->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Service "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Service $service): void
    {
        // Değişen alanları tespit et
        $dirty = $service->getDirty();



        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $service->service_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $service->service_id);
                    }
                }
                $service->slug = $dirty['slug'];
            }
        }

        Log::info('Service updating', [
            'service_id' => $service->service_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Service "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Service $service): void
    {
        // Cache temizle
        $this->clearServiceCaches($service->service_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $service->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                // Eski başlığı al (title değiştiyse)
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $service->getOriginal('title');
                }

                log_activity($service, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Service updated successfully', [
            'service_id' => $service->service_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Service "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Service $service): void
    {
        // Title ve slug validasyon
        if (is_array($service->title)) {
            foreach ($service->title as $locale => $title) {
                $minLength = config('service.validation.title.min', 3);
                $maxLength = config('service.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw ServiceValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $service->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Service title auto-trimmed', [
                            'service_id' => $service->service_id,
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
     * Handle the Service "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Service $service): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_service_{$service->service_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('service.show', $service->slug));
        }

        // ✅ Sitemap cache'ini temizle (gerçek zamanlı güncelleme için)
        $tenantId = tenant()?->id ?? 'central';
        Cache::forget("sitemap_xml_{$tenantId}");
    }

    /**
     * Handle the Service "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Service $service): bool
    {
        // Reserved slug kontrolü
        $reservedSlugs = config('service.slug.reserved_slugs', []);
        if (is_array($service->slug)) {
            foreach ($service->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw ServiceProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('Service deleting', [
            'service_id' => $service->service_id,
            'title' => $service->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Service "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Service $service): void
    {
        // Spatie Media Library - Görselleri temizle
        $service->clearMediaCollection('featured_image');
        $service->clearMediaCollection('gallery');

        // Cache temizle
        $this->clearServiceCaches($service->service_id);

        // ✅ Sitemap cache'ini temizle (gerçek zamanlı güncelleme için)
        $tenantId = tenant()?->id ?? 'central';
        Cache::forget("sitemap_xml_{$tenantId}");

        // SEO ayarlarını da sil
        if ($service->seoSetting) {
            $service->seoSetting->delete();
        }

        // Activity log - silinen kaydın başlığını sakla
        if (function_exists('log_activity')) {
            log_activity($service, 'silindi', null, $service->title);
        }

        Log::info('Service deleted successfully', [
            'service_id' => $service->service_id,
            'title' => $service->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Service "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Service $service): void
    {
        Log::info('Service restoring', [
            'service_id' => $service->service_id,
            'title' => $service->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Service "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Service $service): void
    {
        // Cache temizle
        $this->clearServiceCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($service, 'geri yüklendi');
        }

        Log::info('Service restored successfully', [
            'service_id' => $service->service_id,
            'title' => $service->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Service "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Service $service): bool
    {
        Log::warning('Service force deleting', [
            'service_id' => $service->service_id,
            'title' => $service->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Service "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Service $service): void
    {
        // Spatie Media Library - Görselleri temizle
        $service->clearMediaCollection('featured_image');
        $service->clearMediaCollection('gallery');

        // Tüm cache'leri temizle
        $this->clearServiceCaches($service->service_id);

        // Activity log - kalıcı silme
        if (function_exists('log_activity')) {
            log_activity($service, 'kalıcı silindi', null, $service->title);
        }

        Log::warning('Service force deleted', [
            'service_id' => $service->service_id,
            'title' => $service->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Service cache'lerini temizle
     */
    private function clearServiceCaches(?int $serviceId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('services');

        // Spesifik cache key'leri temizle
        Cache::forget('services_list');
        Cache::forget('services_menu_cache');
        Cache::forget('services_sitemap_cache');

        // Sitemap XML cache temizle
        $tenantId = tenant()?->id ?? 'central';
        Cache::forget("sitemap_xml_{$tenantId}");

        if ($serviceId) {
            Cache::forget("service_detail_{$serviceId}");
            Cache::forget("universal_seo_service_{$serviceId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['services', 'content'])->flush();
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
        $query = Service::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('service_id', '!=', $excludeId);
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
