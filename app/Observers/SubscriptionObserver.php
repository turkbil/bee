<?php

namespace App\Observers;

use Modules\Subscription\App\Models\Subscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "updated" event.
     * Subscription status değişince (active, cancelled, expired) cache temizle
     */
    public function updated(Subscription $subscription): void
    {
        $this->clearUserPremiumCache($subscription->user_id);
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
}
