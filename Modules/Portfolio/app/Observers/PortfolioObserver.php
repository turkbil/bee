<?php

namespace Modules\Portfolio\App\Observers;

use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Exceptions\PortfolioValidationException;
use Modules\Portfolio\App\Exceptions\PortfolioProtectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Portfolio Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class PortfolioObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Portfolio "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Portfolio $portfolio): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($portfolio->slug) && !empty($portfolio->title)) {
            $slugs = [];
            foreach ($portfolio->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $portfolio->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('portfolio.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($portfolio->$field)) {
                $portfolio->$field = $value;
            }
        }


        Log::info('Portfolio creating', [
            'title' => $portfolio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Portfolio "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Portfolio $portfolio): void
    {
        // Cache temizle
        $this->clearPortfolioCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($portfolio, 'oluşturuldu');
        }

        Log::info('Portfolio created successfully', [
            'portfolio_id' => $portfolio->portfolio_id,
            'title' => $portfolio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Portfolio "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Portfolio $portfolio): void
    {
        // Değişen alanları tespit et
        $dirty = $portfolio->getDirty();



        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $portfolio->portfolio_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $portfolio->portfolio_id);
                    }
                }
                $portfolio->slug = $dirty['slug'];
            }
        }

        Log::info('Portfolio updating', [
            'portfolio_id' => $portfolio->portfolio_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Portfolio "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Portfolio $portfolio): void
    {
        // Cache temizle
        $this->clearPortfolioCaches($portfolio->portfolio_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $portfolio->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                log_activity($portfolio, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('Portfolio updated successfully', [
            'portfolio_id' => $portfolio->portfolio_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Portfolio "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Portfolio $portfolio): void
    {
        // Title ve slug validasyon
        if (is_array($portfolio->title)) {
            foreach ($portfolio->title as $locale => $title) {
                $minLength = config('portfolio.validation.title.min', 3);
                $maxLength = config('portfolio.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw PortfolioValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $portfolio->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Portfolio title auto-trimmed', [
                            'portfolio_id' => $portfolio->portfolio_id,
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
     * Handle the Portfolio "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Portfolio $portfolio): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_portfolio_{$portfolio->portfolio_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('portfolio.show', $portfolio->slug));
        }
    }

    /**
     * Handle the Portfolio "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Portfolio $portfolio): bool
    {
        // Reserved slug kontrolü
        $reservedSlugs = config('portfolio.slug.reserved_slugs', []);
        if (is_array($portfolio->slug)) {
            foreach ($portfolio->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw PortfolioProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('Portfolio deleting', [
            'portfolio_id' => $portfolio->portfolio_id,
            'title' => $portfolio->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Portfolio "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Portfolio $portfolio): void
    {
        // Spatie Media Library - Görselleri temizle
        $portfolio->clearMediaCollection('featured_image');
        $portfolio->clearMediaCollection('gallery');

        // Cache temizle
        $this->clearPortfolioCaches($portfolio->portfolio_id);

        // SEO ayarlarını da sil
        if ($portfolio->seoSetting) {
            $portfolio->seoSetting->delete();
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($portfolio, 'silindi');
        }

        Log::info('Portfolio deleted successfully', [
            'portfolio_id' => $portfolio->portfolio_id,
            'title' => $portfolio->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Portfolio "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Portfolio $portfolio): void
    {
        Log::info('Portfolio restoring', [
            'portfolio_id' => $portfolio->portfolio_id,
            'title' => $portfolio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Portfolio "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Portfolio $portfolio): void
    {
        // Cache temizle
        $this->clearPortfolioCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($portfolio, 'geri yüklendi');
        }

        Log::info('Portfolio restored successfully', [
            'portfolio_id' => $portfolio->portfolio_id,
            'title' => $portfolio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Portfolio "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Portfolio $portfolio): bool
    {
        Log::warning('Portfolio force deleting', [
            'portfolio_id' => $portfolio->portfolio_id,
            'title' => $portfolio->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Portfolio "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Portfolio $portfolio): void
    {
        // Spatie Media Library - Görselleri temizle
        $portfolio->clearMediaCollection('featured_image');
        $portfolio->clearMediaCollection('gallery');

        // Tüm cache'leri temizle
        $this->clearPortfolioCaches($portfolio->portfolio_id);

        Log::warning('Portfolio force deleted', [
            'portfolio_id' => $portfolio->portfolio_id,
            'title' => $portfolio->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Portfolio cache'lerini temizle
     */
    private function clearPortfolioCaches(?int $portfolioId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('portfolios');

        // Spesifik cache key'leri temizle
        Cache::forget('portfolios_list');
        Cache::forget('portfolios_menu_cache');
        Cache::forget('portfolios_sitemap_cache');

        if ($portfolioId) {
            Cache::forget("portfolio_detail_{$portfolioId}");
            Cache::forget("universal_seo_portfolio_{$portfolioId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['portfolios', 'content'])->flush();
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
        $query = Portfolio::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('portfolio_id', '!=', $excludeId);
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
