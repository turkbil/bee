<?php

namespace Modules\Subscription\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Modules\Cart\App\Services\CartService;
use Modules\Subscription\App\Services\SubscriptionCartBridge;

class SubscriptionPlansComponent extends Component
{
    public $plans;
    public $userHasUsedTrial = false;

    public function mount()
    {
        // Aktif ve public olan planları getir
        $this->plans = SubscriptionPlan::where('is_active', true)
            ->where('is_public', true)
            ->orderBy('sort_order')
            ->get();

        // Kullanıcı daha önce trial kullandı mı kontrol et
        if (auth()->check()) {
            $this->userHasUsedTrial = \Modules\Subscription\App\Models\Subscription::userHasUsedTrial(auth()->id());
        }
    }

    /**
     * Start trial subscription instantly (no checkout)
     */
    public function startTrial($planId, $cycleKey)
    {
        try {
            // 1. Login kontrolü
            if (!auth()->check()) {
                session()->flash('info', 'Deneme sürümünü başlatmak için giriş yapmalısınız.');
                return redirect()->route('login');
            }

            // 2. Plan kontrolü
            $plan = SubscriptionPlan::findOrFail($planId);

            if (!$plan->is_trial) {
                throw new \Exception('Bu plan deneme planı değil!');
            }

            // 3. User trial kontrolü
            if (auth()->user()->has_used_trial) {
                session()->flash('error', 'Deneme sürümünü daha önce kullandınız.');
                return;
            }

            // 4. Subscription oluştur
            $service = app(\Modules\Subscription\App\Services\SubscriptionService::class);
            $subscription = $service->createTrialForUser(auth()->user());

            if (!$subscription) {
                throw new \Exception('Deneme sürümü oluşturulamadı. Lütfen destek ile iletişime geçin.');
            }

            \Log::info('✅ Trial Subscription Created', [
                'user_id' => auth()->id(),
                'subscription_id' => $subscription->subscription_id,
            ]);

            // 5. Session yenile (premium tanıması için)
            auth()->user()->refresh();
            session()->regenerate();

            // 6. Başarı sayfasına yönlendir (FULL PAGE RELOAD)
            return $this->redirect(route('subscription.success', ['trial' => 1]), navigate: false);

        } catch (\Exception $e) {
            \Log::error('❌ Trial Subscription ERROR', [
                'user_id' => auth()->id() ?? null,
                'plan_id' => $planId ?? null,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function addToCart($planId, $cycleKey, $autoRenew = true)
    {
        try {
            $plan = SubscriptionPlan::findOrFail($planId);

            // Bridge service kullan (Shop ile aynı pattern)
            $bridge = app(SubscriptionCartBridge::class);

            // Plan eklenebilir mi kontrol et
            if (!$bridge->canAddToCart($plan)) {
                $errors = $bridge->getCartItemErrors($plan);
                throw new \Exception(implode(' ', $errors));
            }

            // CartService kullan (Shop ile aynı mantık)
            $cartService = app(CartService::class);

            // Session/Customer
            $sessionId = session()->getId();
            $customerId = auth()->check() ? auth()->id() : null;

            // Cart bul/oluştur
            $cart = $cartService->findOrCreateCart($customerId, $sessionId);

            // Cart'taki diğer subscription'ları temizle (sadece bir subscription olabilir)
            $cart->items()
                ->where('cartable_type', 'Modules\Subscription\App\Models\SubscriptionPlan')
                ->each(function ($item) use ($cartService) {
                    $cartService->removeItem($item);
                });

            // Bridge ile subscription verilerini hazırla (fiyat + display info)
            $options = $bridge->prepareSubscriptionForCart($plan, $cycleKey, $autoRenew);

            // Cart'a ekle (CartService ile)
            $cartItem = $cartService->addItem($cart, $plan, 1, $options);

            // Cart refresh
            $cart->refresh();
            $itemCount = $cart->items()->where('is_active', true)->sum('quantity');

            // Events
            $this->dispatch('cartUpdated');

            \Log::info('✅ Subscription AddToCart SUCCESS', [
                'plan_id' => $planId,
                'cart_id' => $cart->cart_id,
            ]);

            // Checkout sayfasına yönlendir (Livewire navigate)
            return $this->redirect(route('cart.checkout'), navigate: true);

        } catch (\Exception $e) {
            \Log::error('❌ Subscription AddToCart ERROR', [
                'plan_id' => $planId ?? null,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Tema-aware view ve layout
        $theme = tenant()->theme ?? 'ixtif';

        // Önce tema-specific view'ı dene, yoksa default kullan
        $viewPath = "themes.{$theme}.subscription-plans";
        $defaultViewPath = 'subscription::livewire.front.subscription-plans';

        // Layout - Tema-aware
        $layoutPath = "themes.{$theme}.layouts.app";

        if (view()->exists($viewPath)) {
            return view($viewPath, [
                'plans' => $this->plans,
            ])->layout($layoutPath);
        }

        return view($defaultViewPath, [
            'plans' => $this->plans,
        ])->layout($layoutPath);
    }
}
