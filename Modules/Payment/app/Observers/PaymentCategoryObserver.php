<?php

namespace Modules\Payment\App\Observers;

use Modules\Payment\App\Models\PaymentCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * PaymentCategory Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class PaymentCategoryObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the PaymentCategory "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(PaymentCategory $category): void
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

        Log::info('Payment Category creating', [
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PaymentCategory "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(PaymentCategory $category): void
    {
        // Cache temizle
        $this->clearCategoryCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($category, 'oluşturuldu');
        }

        Log::info('Payment Category created successfully', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PaymentCategory "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(PaymentCategory $category): void
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

        Log::info('Payment Category updating', [
            'category_id' => $category->category_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PaymentCategory "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(PaymentCategory $category): void
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

        Log::info('Payment Category updated successfully', [
            'category_id' => $category->category_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PaymentCategory "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(PaymentCategory $category): void
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

                        Log::warning('Payment Category title auto-trimmed', [
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
     * Handle the PaymentCategory "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(PaymentCategory $category): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_payment_category_{$category->category_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('payment.category.show', $category->slug));
        }
    }

    /**
     * Handle the PaymentCategory "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(PaymentCategory $category): bool
    {
        // Kategoriye bağlı paymentlar varsa silme
        if ($category->payments()->count() > 0) {
            throw new \Exception('Bu kategoriye ait paymentlar var. Önce paymentları silmelisiniz.');
        }

        Log::info('Payment Category deleting', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the PaymentCategory "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(PaymentCategory $category): void
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

        Log::info('Payment Category deleted successfully', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PaymentCategory "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(PaymentCategory $category): void
    {
        Log::info('Payment Category restoring', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PaymentCategory "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(PaymentCategory $category): void
    {
        // Cache temizle
        $this->clearCategoryCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($category, 'geri yüklendi');
        }

        Log::info('Payment Category restored successfully', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the PaymentCategory "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(PaymentCategory $category): bool
    {
        Log::warning('Payment Category force deleting', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the PaymentCategory "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(PaymentCategory $category): void
    {
        // Tüm cache'leri temizle
        $this->clearCategoryCaches($category->category_id);

        Log::warning('Payment Category force deleted', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Payment Category cache'lerini temizle
     */
    private function clearCategoryCaches(?int $categoryId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('payment_categories');

        // Spesifik cache key'leri temizle
        Cache::forget('payment_categories_list');
        Cache::forget('payment_categories_menu_cache');

        if ($categoryId) {
            Cache::forget("payment_category_detail_{$categoryId}");
            Cache::forget("universal_seo_payment_category_{$categoryId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['payment_categories', 'content'])->flush();
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
        $query = PaymentCategory::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

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
