<?php

namespace Modules\Portfolio\App\Observers;

use Modules\Portfolio\App\Models\PortfolioCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * PortfolioCategory Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class PortfolioCategoryObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the PortfolioCategory "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(PortfolioCategory $category): void
    {
        // Slug yoksa name'den otomatik oluştur
        if (empty($category->slug) && !empty($category->name)) {
            $slugs = [];
            foreach ($category->name as $locale => $name) {
                if (!empty($name)) {
                    $slugs[$locale] = Str::slug($name);
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

        Log::info('Portfolio Category creating', [
            'name' => $category->name,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PortfolioCategory "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(PortfolioCategory $category): void
    {
        // Cache temizle
        $this->clearCategoryCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($category, 'oluşturuldu');
        }

        Log::info('Portfolio Category created successfully', [
            'category_id' => $category->category_id,
            'name' => $category->name,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PortfolioCategory "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(PortfolioCategory $category): void
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

        Log::info('Portfolio Category updating', [
            'category_id' => $category->category_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PortfolioCategory "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(PortfolioCategory $category): void
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

        Log::info('Portfolio Category updated successfully', [
            'category_id' => $category->category_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PortfolioCategory "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(PortfolioCategory $category): void
    {
        // Name validasyon
        if (is_array($category->name)) {
            foreach ($category->name as $locale => $name) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($name)) {
                    // Minimum length check
                    if (strlen($name) < $minLength) {
                        throw new \Exception("Kategori adı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    // Maximum length check - auto trim
                    if (strlen($name) > $maxLength) {
                        $category->name[$locale] = mb_substr($name, 0, $maxLength);

                        Log::warning('Portfolio Category name auto-trimmed', [
                            'category_id' => $category->category_id,
                            'locale' => $locale,
                            'original_length' => strlen($name),
                            'trimmed_length' => $maxLength
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Handle the PortfolioCategory "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(PortfolioCategory $category): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_portfolio_category_{$category->category_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('portfolio.category.show', $category->slug));
        }
    }

    /**
     * Handle the PortfolioCategory "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(PortfolioCategory $category): bool
    {
        // Kategoriye bağlı portfoliolar varsa silme
        if ($category->portfolios()->count() > 0) {
            throw new \Exception('Bu kategoriye ait portfoliolar var. Önce portfolioları silmelisiniz.');
        }

        Log::info('Portfolio Category deleting', [
            'category_id' => $category->category_id,
            'name' => $category->name,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the PortfolioCategory "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(PortfolioCategory $category): void
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

        Log::info('Portfolio Category deleted successfully', [
            'category_id' => $category->category_id,
            'name' => $category->name,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PortfolioCategory "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(PortfolioCategory $category): void
    {
        Log::info('Portfolio Category restoring', [
            'category_id' => $category->category_id,
            'name' => $category->name,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PortfolioCategory "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(PortfolioCategory $category): void
    {
        // Cache temizle
        $this->clearCategoryCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($category, 'geri yüklendi');
        }

        Log::info('Portfolio Category restored successfully', [
            'category_id' => $category->category_id,
            'name' => $category->name,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PortfolioCategory "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(PortfolioCategory $category): bool
    {
        Log::warning('Portfolio Category force deleting', [
            'category_id' => $category->category_id,
            'name' => $category->name,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the PortfolioCategory "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(PortfolioCategory $category): void
    {
        // Tüm cache'leri temizle
        $this->clearCategoryCaches($category->category_id);

        Log::warning('Portfolio Category force deleted', [
            'category_id' => $category->category_id,
            'name' => $category->name,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Portfolio Category cache'lerini temizle
     */
    private function clearCategoryCaches(?int $categoryId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('portfolio_categories');

        // Spesifik cache key'leri temizle
        Cache::forget('portfolio_categories_list');
        Cache::forget('portfolio_categories_menu_cache');

        if ($categoryId) {
            Cache::forget("portfolio_category_detail_{$categoryId}");
            Cache::forget("universal_seo_portfolio_category_{$categoryId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['portfolio_categories', 'content'])->flush();
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
        $query = PortfolioCategory::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

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
