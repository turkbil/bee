<?php

namespace Modules\Blog\App\Observers;

use Modules\Blog\App\Models\BlogCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * BlogCategory Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class BlogCategoryObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the BlogCategory "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(BlogCategory $category): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($category->slug) && !empty($category->title)) {
            $slugs = [];
            foreach ($category->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $category->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        if (!isset($category->is_active)) {
            $category->is_active = true;
        }

        if (!isset($category->sort_order)) {
            $category->sort_order = 0;
        }

        Log::info('Blog Category creating', [
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the BlogCategory "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(BlogCategory $category): void
    {
        // Cache temizle
        $this->clearCategoryCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($category, 'oluşturuldu');
        }

        Log::info('Blog Category created successfully', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the BlogCategory "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(BlogCategory $category): void
    {
        // Değişen alanları tespit et
        $dirty = $category->getDirty();

        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $category->category_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $category->category_id);
                    }
                }
                $category->slug = $dirty['slug'];
            }
        }

        Log::info('Blog Category updating', [
            'category_id' => $category->category_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the BlogCategory "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(BlogCategory $category): void
    {
        // Cache temizle
        $this->clearCategoryCaches($category->category_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $category->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                log_activity($category, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('Blog Category updated successfully', [
            'category_id' => $category->category_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the BlogCategory "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(BlogCategory $category): void
    {
        // Title validasyon
        if (is_array($category->title)) {
            foreach ($category->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Kategori başlığı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    // Maximum length check - auto trim
                    if (strlen($title) > $maxLength) {
                        $category->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Blog Category title auto-trimmed', [
                            'category_id' => $category->category_id,
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
     * Handle the BlogCategory "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(BlogCategory $category): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_blog_category_{$category->category_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('blog.category.show', $category->slug));
        }
    }

    /**
     * Handle the BlogCategory "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(BlogCategory $category): bool
    {
        // Kategoriye bağlı bloglar varsa silme
        if ($category->blogs()->count() > 0) {
            throw new \Exception('Bu kategoriye ait bloglar var. Önce blogları silmelisiniz.');
        }

        Log::info('Blog Category deleting', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the BlogCategory "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(BlogCategory $category): void
    {
        // Cache temizle
        $this->clearCategoryCaches($category->category_id);

        // SEO ayarlarını da sil
        if ($category->seoSetting) {
            $category->seoSetting->delete();
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($category, 'silindi');
        }

        Log::info('Blog Category deleted successfully', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the BlogCategory "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(BlogCategory $category): void
    {
        Log::info('Blog Category restoring', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the BlogCategory "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(BlogCategory $category): void
    {
        // Cache temizle
        $this->clearCategoryCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($category, 'geri yüklendi');
        }

        Log::info('Blog Category restored successfully', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the BlogCategory "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(BlogCategory $category): bool
    {
        Log::warning('Blog Category force deleting', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the BlogCategory "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(BlogCategory $category): void
    {
        // Tüm cache'leri temizle
        $this->clearCategoryCaches($category->category_id);

        Log::warning('Blog Category force deleted', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Blog Category cache'lerini temizle
     */
    private function clearCategoryCaches(?int $categoryId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('blog_categories');

        // Spesifik cache key'leri temizle
        Cache::forget('blog_categories_list');
        Cache::forget('blog_categories_menu_cache');

        if ($categoryId) {
            Cache::forget("blog_category_detail_{$categoryId}");
            Cache::forget("universal_seo_blog_category_{$categoryId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['blog_categories', 'content'])->flush();
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
        $query = BlogCategory::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('category_id', '!=', $excludeId);
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
