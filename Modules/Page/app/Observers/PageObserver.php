<?php

namespace Modules\Page\App\Observers;

use Modules\Page\App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Page Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class PageObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Page "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Page $page): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($page->slug) && !empty($page->title)) {
            $slugs = [];
            foreach ($page->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $page->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('page.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($page->$field)) {
                $page->$field = $value;
            }
        }

        // Homepage kontrolü - sadece bir tane olabilir
        if ($page->is_homepage) {
            Page::where('is_homepage', true)->update(['is_homepage' => false]);
        }

        Log::info('Page creating', [
            'title' => $page->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Page "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Page $page): void
    {
        // Cache temizle
        $this->clearPageCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($page, 'oluşturuldu');
        }

        Log::info('Page created successfully', [
            'page_id' => $page->page_id,
            'title' => $page->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Page "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Page $page): void
    {
        // Değişen alanları tespit et
        $dirty = $page->getDirty();

        // Homepage pasif edilemez kontrolü
        if ($page->is_homepage && isset($dirty['is_active']) && $dirty['is_active'] === false) {
            throw HomepageProtectionException::cannotDeactivate($page->page_id);
        }

        // Homepage değişiklik kontrolü
        if (isset($dirty['is_homepage']) && $dirty['is_homepage'] === true) {
            // Diğer tüm homepage'leri false yap
            Page::where('is_homepage', true)
                ->where('page_id', '!=', $page->page_id)
                ->update(['is_homepage' => false]);
        }

        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $page->page_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $page->page_id);
                    }
                }
                $page->slug = $dirty['slug'];
            }
        }

        Log::info('Page updating', [
            'page_id' => $page->page_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Page "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Page $page): void
    {
        // Cache temizle
        $this->clearPageCaches($page->page_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $page->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                log_activity($page, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('Page updated successfully', [
            'page_id' => $page->page_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Page "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Page $page): void
    {
        // CSS/JS boyut kontrolü
        if (config('page.features.custom_css_js', true)) {
            $maxCssSize = config('page.security.max_css_size', 50000);
            $maxJsSize = config('page.security.max_js_size', 50000);

            if ($page->css && strlen($page->css) > $maxCssSize) {
                throw PageValidationException::cssSizeExceeded($maxCssSize);
            }

            if ($page->js && strlen($page->js) > $maxJsSize) {
                throw PageValidationException::jsSizeExceeded($maxJsSize);
            }
        }

        // Title ve slug validasyon
        if (is_array($page->title)) {
            foreach ($page->title as $locale => $title) {
                $minLength = config('page.validation.title.min', 3);
                $maxLength = config('page.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw PageValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $page->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Page title auto-trimmed', [
                            'page_id' => $page->page_id,
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
     * Handle the Page "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Page $page): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_page_{$page->page_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('page.show', $page->slug));
        }

        // ✅ Sitemap cache'ini temizle (gerçek zamanlı güncelleme için)
        $tenantId = tenant()?->id ?? 'central';
        Cache::forget("sitemap_xml_{$tenantId}");
    }

    /**
     * Handle the Page "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Page $page): bool
    {
        // Homepage koruması
        if ($page->is_homepage) {
            throw HomepageProtectionException::cannotDelete($page->page_id);
        }

        // Reserved slug kontrolü
        $reservedSlugs = config('page.slug.reserved_slugs', []);
        if (is_array($page->slug)) {
            foreach ($page->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw PageProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('Page deleting', [
            'page_id' => $page->page_id,
            'title' => $page->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Page "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Page $page): void
    {
        // Cache temizle
        $this->clearPageCaches($page->page_id);

        // SEO ayarlarını da sil
        if ($page->seoSetting) {
            $page->seoSetting->delete();
        }

        // ✅ Sitemap cache'ini temizle (gerçek zamanlı güncelleme için)
        $tenantId = tenant()?->id ?? 'central';
        Cache::forget("sitemap_xml_{$tenantId}");

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($page, 'silindi');
        }

        Log::info('Page deleted successfully', [
            'page_id' => $page->page_id,
            'title' => $page->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Page "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Page $page): void
    {
        Log::info('Page restoring', [
            'page_id' => $page->page_id,
            'title' => $page->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Page "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Page $page): void
    {
        // Cache temizle
        $this->clearPageCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($page, 'geri yüklendi');
        }

        Log::info('Page restored successfully', [
            'page_id' => $page->page_id,
            'title' => $page->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Page "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Page $page): bool
    {
        // Homepage koruması
        if ($page->is_homepage) {
            throw HomepageProtectionException::cannotForceDelete($page->page_id);
        }

        Log::warning('Page force deleting', [
            'page_id' => $page->page_id,
            'title' => $page->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Page "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Page $page): void
    {
        // Tüm cache'leri temizle
        $this->clearPageCaches($page->page_id);

        Log::warning('Page force deleted', [
            'page_id' => $page->page_id,
            'title' => $page->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Page cache'lerini temizle
     */
    private function clearPageCaches(?int $pageId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('pages');

        // Spesifik cache key'leri temizle
        Cache::forget('pages_list');
        Cache::forget('pages_menu_cache');
        Cache::forget('pages_sitemap_cache');
        Cache::forget('homepage_data');

        // Sitemap XML cache temizle (yeni eklenen içerik için)
        $tenantId = tenant()?->id ?? 'central';
        Cache::forget("sitemap_xml_{$tenantId}");

        if ($pageId) {
            Cache::forget("page_detail_{$pageId}");
            Cache::forget("universal_seo_page_{$pageId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['pages', 'content'])->flush();
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
        $query = Page::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('page_id', '!=', $excludeId);
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
