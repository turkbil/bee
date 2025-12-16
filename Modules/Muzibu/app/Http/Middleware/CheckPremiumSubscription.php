<?php

namespace Modules\Muzibu\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Check Premium Subscription Middleware
 *
 * Tenant 1001 (Muzibu) için Premium subscription kontrolü
 * Müzik çalma endpoint'leri Premium üyelere özeldir
 */
class CheckPremiumSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen giriş yapın',
                'error_code' => 'AUTH_REQUIRED'
            ], 401);
        }

        // 2. Check if user has premium subscription
        $userId = auth()->id();
        $hasPremium = $this->checkPremiumSubscription($userId);

        if (!$hasPremium) {
            return response()->json([
                'success' => false,
                'message' => 'Bu özellik Premium üyelere özeldir',
                'error_code' => 'PREMIUM_REQUIRED',
                'upgrade_url' => url('/subscription/plans')
            ], 403);
        }

        // 3. User has premium, continue
        return $next($request);
    }

    /**
     * Check if user has active premium subscription
     *
     * @param int $userId
     * @return bool
     */
    protected function checkPremiumSubscription(int $userId): bool
    {
        try {
            // Query central database for active subscription
            $subscription = DB::connection('central')
                ->table('subscriptions')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->first();

            $hasPremium = $subscription !== null;

            Log::info('Premium check', [
                'user_id' => $userId,
                'has_premium' => $hasPremium,
                'subscription_id' => $subscription->id ?? null,
            ]);

            return $hasPremium;

        } catch (\Exception $e) {
            Log::error('Premium check error', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            // On error, deny access (fail-safe)
            return false;
        }
    }
}
