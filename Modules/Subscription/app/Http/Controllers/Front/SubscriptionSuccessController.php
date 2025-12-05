<?php

namespace Modules\Subscription\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionSuccessController extends Controller
{
    /**
     * Show subscription success page
     */
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('home');
        }

        // Get active subscription
        $subscription = $user->activeSubscription()->with('plan')->first();

        if (!$subscription) {
            session()->flash('info', 'Aktif abonelik bulunamadı.');
            return redirect()->route('subscription.plans');
        }

        $isTrial = $request->query('trial', false);

        // Theme-aware view
        $theme = tenant()->theme ?? 'ixtif';
        $viewPath = "themes.{$theme}.subscription-success";

        if (!view()->exists($viewPath)) {
            $viewPath = 'subscription::front.subscription-success';
        }

        return view($viewPath, [
            'subscription' => $subscription,
            'isTrial' => $isTrial,
            'user' => $user,
        ]);
    }
}
