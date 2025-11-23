<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if paid membership is enabled
        $isPaidEnabled = (bool) setting('auth_subscription_paid_enabled', false);

        if (!$isPaidEnabled) {
            // Paid membership not required for this tenant
            return $next($request);
        }

        // Check for active subscription (including parent's for sub-accounts)
        $subscription = $user->getCorporateSubscription();

        if (!$subscription || !$subscription->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Bu içeriğe erişmek için aktif bir abonelik gerekiyor.',
                    'error' => 'subscription_required',
                    'redirect' => route('subscription.plans'),
                ], 403);
            }

            return redirect()
                ->route('subscription.plans')
                ->with('error', 'Bu içeriğe erişmek için aktif bir abonelik gerekiyor.');
        }

        return $next($request);
    }
}
