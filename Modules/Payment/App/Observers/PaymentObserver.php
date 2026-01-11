<?php

namespace Modules\Payment\App\Observers;

use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Exceptions\PaymentValidationException;
use Modules\Payment\App\Exceptions\PaymentProtectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Payment Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class PaymentObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Payment "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Payment $payment): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($payment->slug) && !empty($payment->title)) {
            $slugs = [];
            foreach ($payment->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $payment->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('payment.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($payment->$field)) {
                $payment->$field = $value;
            }
        }


        Log::info('Payment creating', [
            'title' => $payment->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Payment "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Payment $payment): void
    {
        // Cache temizle
        $this->clearPaymentCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($payment, 'oluşturuldu');
        }

        Log::info('Payment created successfully', [
            'payment_id' => $payment->payment_id,
            'title' => $payment->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Payment "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Payment $payment): void
    {
        // Değişen alanları tespit et
        $dirty = $payment->getDirty();



        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $payment->payment_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $payment->payment_id);
                    }
                }
                $payment->slug = $dirty['slug'];
            }
        }

        Log::info('Payment updating', [
            'payment_id' => $payment->payment_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Payment "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Payment $payment): void
    {
        // Cache temizle
        $this->clearPaymentCaches($payment->payment_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $payment->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                log_activity($payment, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('Payment updated successfully', [
            'payment_id' => $payment->payment_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Payment "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Payment $payment): void
    {
        // Title ve slug validasyon
        if (is_array($payment->title)) {
            foreach ($payment->title as $locale => $title) {
                $minLength = config('payment.validation.title.min', 3);
                $maxLength = config('payment.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw PaymentValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $payment->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Payment title auto-trimmed', [
                            'payment_id' => $payment->payment_id,
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
     * Handle the Payment "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Payment $payment): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_payment_{$payment->payment_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('payment.show', $payment->slug));
        }
    }

    /**
     * Handle the Payment "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Payment $payment): bool
    {
        // Reserved slug kontrolü
        $reservedSlugs = config('payment.slug.reserved_slugs', []);
        if (is_array($payment->slug)) {
            foreach ($payment->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw PaymentProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('Payment deleting', [
            'payment_id' => $payment->payment_id,
            'title' => $payment->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Payment "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Payment $payment): void
    {
        // Spatie Media Library - Görselleri temizle
        $payment->clearMediaCollection('featured_image');
        $payment->clearMediaCollection('gallery');

        // Cache temizle
        $this->clearPaymentCaches($payment->payment_id);

        // SEO ayarlarını da sil
        if ($payment->seoSetting) {
            $payment->seoSetting->delete();
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($payment, 'silindi');
        }

        Log::info('Payment deleted successfully', [
            'payment_id' => $payment->payment_id,
            'title' => $payment->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Payment "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Payment $payment): void
    {
        Log::info('Payment restoring', [
            'payment_id' => $payment->payment_id,
            'title' => $payment->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Payment "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Payment $payment): void
    {
        // Cache temizle
        $this->clearPaymentCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($payment, 'geri yüklendi');
        }

        Log::info('Payment restored successfully', [
            'payment_id' => $payment->payment_id,
            'title' => $payment->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Payment "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Payment $payment): bool
    {
        Log::warning('Payment force deleting', [
            'payment_id' => $payment->payment_id,
            'title' => $payment->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Payment "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Payment $payment): void
    {
        // Spatie Media Library - Görselleri temizle
        $payment->clearMediaCollection('featured_image');
        $payment->clearMediaCollection('gallery');

        // Tüm cache'leri temizle
        $this->clearPaymentCaches($payment->payment_id);

        Log::warning('Payment force deleted', [
            'payment_id' => $payment->payment_id,
            'title' => $payment->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Payment cache'lerini temizle
     */
    private function clearPaymentCaches(?int $paymentId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('payments');

        // Spesifik cache key'leri temizle
        Cache::forget('payments_list');
        Cache::forget('payments_menu_cache');
        Cache::forget('payments_sitemap_cache');

        if ($paymentId) {
            Cache::forget("payment_detail_{$paymentId}");
            Cache::forget("universal_seo_payment_{$paymentId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['payments', 'content'])->flush();
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
        $query = Payment::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('payment_id', '!=', $excludeId);
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
