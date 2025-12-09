<?php

namespace App\Observers;

use Modules\Subscription\App\Models\Subscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "updated" event.
     * Subscription status değişince (active, cancelled, expired) cache temizle
     * Plan değişince (downgrade) device limit enforce et
     */
    public function updated(Subscription $subscription): void
    {
        $this->clearUserPremiumCache($subscription->user_id);

        // 🔐 Plan değişikliği veya iptal durumunda device limit enforce et
        $this->enforceDeviceLimitIfNeeded($subscription);
    }

    /**
     * Handle the Subscription "created" event.
     * Yeni subscription oluşunca cache temizle
     */
    public function created(Subscription $subscription): void
    {
        $this->clearUserPremiumCache($subscription->user_id);
    }

    /**
     * Handle the Subscription "deleted" event.
     * Subscription silinince cache temizle
     */
    public function deleted(Subscription $subscription): void
    {
        $this->clearUserPremiumCache($subscription->user_id);
    }

    /**
     * Clear user premium cache
     * 🔥 Cache key matches User::isPremium() method
     */
    protected function clearUserPremiumCache(int $userId): void
    {
        // Tenant ID (central veya multi-tenant)
        $tenantId = tenant() ? tenant()->id : 1;

        // Cache key (MUST match User::isPremium() exactly!)
        $cacheKey = 'user_' . $userId . '_is_premium_tenant_' . $tenantId;

        Cache::forget($cacheKey);

        Log::info('🔥 SubscriptionObserver: Premium cache cleared', [
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'cache_key' => $cacheKey
        ]);
    }

    /**
     * 🔐 Enforce device limit on plan change or subscription termination
     * Triggers when:
     * - subscription_plan_id changes (downgrade)
     * - status becomes cancelled/expired
     */
    protected function enforceDeviceLimitIfNeeded(Subscription $subscription): void
    {
        // Sadece tenant context'te çalış (Muzibu vb.)
        if (!tenant()) {
            return;
        }

        // Değişen field'ları kontrol et
        $statusChanged = $subscription->wasChanged('status');
        $planChanged = $subscription->wasChanged('subscription_plan_id');

        // Subscription iptal/expire olduysa
        $isTerminated = in_array($subscription->status, ['cancelled', 'expired']);

        // Plan değişti veya subscription terminate olduysa device limit enforce et
        if ($planChanged || ($statusChanged && $isTerminated)) {
            try {
                $user = User::find($subscription->user_id);

                if (!$user) {
                    Log::warning('🔐 SubscriptionObserver: User not found for device limit enforcement', [
                        'user_id' => $subscription->user_id,
                    ]);
                    return;
                }

                // DeviceService kullan (tenant-aware)
                $deviceService = app(\Modules\Muzibu\App\Services\DeviceService::class);

                // Subscription terminate olduysa default limit'e düş (1 cihaz)
                if ($isTerminated) {
                    $newLimit = (int) setting('auth_device_limit', 1);
                    $terminatedCount = $deviceService->enforceDeviceLimitOnPlanChange($user, $newLimit);
                } else {
                    // Plan değişti, yeni plan'ın limit'ini kullan
                    $terminatedCount = $deviceService->enforceDeviceLimitOnPlanChange($user);
                }

                if ($terminatedCount > 0) {
                    Log::info('🔐 SubscriptionObserver: Device limit enforced', [
                        'user_id' => $subscription->user_id,
                        'plan_changed' => $planChanged,
                        'status_changed' => $statusChanged,
                        'new_status' => $subscription->status,
                        'terminated_sessions' => $terminatedCount,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('🔐 SubscriptionObserver: Device limit enforcement failed', [
                    'user_id' => $subscription->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
