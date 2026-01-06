<?php

namespace Modules\Subscription\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Subscription\App\Models\Subscription;
use Modules\Cart\App\Models\Order;

class SubscriptionCheckoutController extends Controller
{
    /**
     * Subscription için ödeme sayfasına yönlendir
     * Eğer order yoksa oluştur
     */
    public function show($subscriptionId)
    {
        \Log::info('🔍 SubscriptionCheckoutController::show called', [
            'subscription_id' => $subscriptionId,
            'user_id' => auth()->id(),
            'auth_check' => auth()->check(),
        ]);

        $subscription = Subscription::find($subscriptionId);

        if (!$subscription) {
            return redirect()->route('dashboard')
                ->with('error', 'Abonelik bulunamadı.');
        }

        // Kullanıcı kontrolü
        if ($subscription->user_id !== auth()->id()) {
            return redirect()->route('dashboard')
                ->with('error', 'Bu aboneliğe erişim yetkiniz yok.');
        }

        // Subscription için order var mı kontrol et
        $order = Order::where('user_id', auth()->id())
            ->whereJsonContains('metadata->subscription_id', $subscription->subscription_id)
            ->whereIn('payment_status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Order varsa payment sayfasına yönlendir
        if ($order) {
            return redirect()->route('payment.page', ['orderNumber' => $order->order_number]);
        }

        // Order yoksa checkout'a yönlendir - subscription ID ile
        return redirect()->route('cart.checkout', ['subscription_id' => $subscription->subscription_id])
            ->with('info', 'Lütfen ödeme bilgilerinizi girin.');
    }
}
