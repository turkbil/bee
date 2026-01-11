<?php

namespace Modules\Coupon\App\Observers;

use Modules\Coupon\App\Models\Coupon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Coupon Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Activity logging, cache temizleme ve validasyon işlemlerini otomatikleştirir.
 */
class CouponObserver
{
    /**
     * Handle the Coupon "creating" event.
     */
    public function creating(Coupon $coupon): void
    {
        // Varsayılan değerleri ayarla
        if (!isset($coupon->is_active)) {
            $coupon->is_active = true;
        }

        if (!isset($coupon->is_public)) {
            $coupon->is_public = false;
        }

        if (!isset($coupon->used_count)) {
            $coupon->used_count = 0;
        }

        Log::info('Coupon creating', [
            'title' => $coupon->title,
            'code' => $coupon->code,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Coupon "created" event.
     */
    public function created(Coupon $coupon): void
    {
        $this->clearCouponCaches();

        if (function_exists('log_activity')) {
            log_activity($coupon, 'oluşturuldu');
        }

        Log::info('Coupon created successfully', [
            'coupon_id' => $coupon->coupon_id,
            'code' => $coupon->code,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Coupon "updating" event.
     */
    public function updating(Coupon $coupon): void
    {
        $dirty = $coupon->getDirty();

        Log::info('Coupon updating', [
            'coupon_id' => $coupon->coupon_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Coupon "updated" event.
     */
    public function updated(Coupon $coupon): void
    {
        $this->clearCouponCaches($coupon->coupon_id);

        if (function_exists('log_activity')) {
            $changes = $coupon->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $coupon->getOriginal('title');
                }

                log_activity($coupon, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Coupon updated successfully', [
            'coupon_id' => $coupon->coupon_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Coupon "saving" event.
     */
    public function saving(Coupon $coupon): void
    {
        // Code validation - must be uppercase, alphanumeric
        if (!empty($coupon->code)) {
            $coupon->code = strtoupper(trim($coupon->code));
        }

        // Title validation
        if (is_array($coupon->title)) {
            foreach ($coupon->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Kupon adı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    if (strlen($title) > $maxLength) {
                        $coupon->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Coupon title auto-trimmed', [
                            'coupon_id' => $coupon->coupon_id,
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
     * Handle the Coupon "saved" event.
     */
    public function saved(Coupon $coupon): void
    {
        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the Coupon "deleting" event.
     */
    public function deleting(Coupon $coupon): bool
    {
        // Kullanılmış kuponlar için uyarı log
        if ($coupon->used_count > 0) {
            Log::warning('Deleting coupon that has been used', [
                'coupon_id' => $coupon->coupon_id,
                'code' => $coupon->code,
                'used_count' => $coupon->used_count,
                'user_id' => auth()->id()
            ]);
        }

        Log::info('Coupon deleting', [
            'coupon_id' => $coupon->coupon_id,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Coupon "deleted" event.
     */
    public function deleted(Coupon $coupon): void
    {
        $this->clearCouponCaches($coupon->coupon_id);

        if (function_exists('log_activity')) {
            log_activity($coupon, 'silindi', null, $coupon->title);
        }

        Log::info('Coupon deleted successfully', [
            'coupon_id' => $coupon->coupon_id,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Coupon "restoring" event.
     */
    public function restoring(Coupon $coupon): void
    {
        Log::info('Coupon restoring', [
            'coupon_id' => $coupon->coupon_id,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Coupon "restored" event.
     */
    public function restored(Coupon $coupon): void
    {
        $this->clearCouponCaches();

        if (function_exists('log_activity')) {
            log_activity($coupon, 'geri yüklendi');
        }

        Log::info('Coupon restored successfully', [
            'coupon_id' => $coupon->coupon_id,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Coupon "forceDeleting" event.
     */
    public function forceDeleting(Coupon $coupon): bool
    {
        Log::warning('Coupon force deleting', [
            'coupon_id' => $coupon->coupon_id,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Coupon "forceDeleted" event.
     */
    public function forceDeleted(Coupon $coupon): void
    {
        $this->clearCouponCaches($coupon->coupon_id);

        if (function_exists('log_activity')) {
            log_activity($coupon, 'kalıcı silindi', null, $coupon->title);
        }

        Log::warning('Coupon force deleted', [
            'coupon_id' => $coupon->coupon_id,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Clear coupon related caches
     */
    private function clearCouponCaches(?int $couponId = null): void
    {
        Cache::forget('coupons_list');
        Cache::forget('coupons_active');
        Cache::forget('coupons_public');
        Cache::forget('coupons_valid');

        if ($couponId) {
            Cache::forget("coupon_detail_{$couponId}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['coupons', 'shop', 'cart'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }
}
