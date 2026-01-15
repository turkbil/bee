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
    public $hasEnoughSubscription = false;
    public $remainingDays = 0;
    public $expiresAt = null;
    public $isPremium = false;
    public $isGuest = true;
    public $trialDays = 0;
    public $daysLeft = 0;

    public function mount()
    {
        // Guest/Auth kontrolü
        $this->isGuest = !auth()->check();

        // Kullanıcının mevcut abonelik durumunu kontrol et
        if (auth()->check()) {
            $user = auth()->user();
            $expiresAt = $user->subscription_expires_at;

            // Premium kontrolü
            if ($expiresAt && $expiresAt->isFuture()) {
                $this->isPremium = true;
                $this->daysLeft = (int) now()->diffInDays($expiresAt, false);

                // 30 günden fazla süresi varsa planları gösterme
                if ($this->daysLeft > 30) {
                    $this->hasEnoughSubscription = true;
                    $this->remainingDays = $this->daysLeft;
                    $this->expiresAt = $expiresAt->format('d.m.Y');
                    $this->plans = collect(); // Boş collection
                    return;
                }
            }

            // Trial kontrolü
            $this->userHasUsedTrial = \Modules\Subscription\App\Models\Subscription::userHasUsedTrial(auth()->id());
        }

        // Trial plan'ın gün sayısını al (guest kullanıcılar için buton metninde kullanılacak)
        // NOT: Trial plan gizli (is_public=false) olsa bile gün sayısını alıyoruz (buton metni için)
        // Gizli olduğu için plan listesinde görünmeyecek, sadece "Üye Ol, X Gün Ücretsiz Dinle" yazacak
        $trialPlan = SubscriptionPlan::where('is_trial', true)
            ->where('is_active', true)
            ->first(); // is_public kontrolü YOK!

        if ($trialPlan) {
            $cycles = $trialPlan->getSortedCycles();
            if (!empty($cycles)) {
                $firstCycle = reset($cycles);
                $this->trialDays = $firstCycle['duration_days'] ?? 0;
            }
        }

        // Aktif ve public olan planları getir (is_public=false gizli planlar otomatik filtrelenir)
        $this->plans = SubscriptionPlan::where('is_active', true)
            ->where('is_public', true)
            ->orderBy('sort_order')
            ->get();
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
            // 🔐 Guest kullanıcıyı login'e yönlendir (sepete ekleme yok)
            if (!auth()->check()) {
                // Login sonrası geri dönmesi için URL'yi kaydet
                session()->put('url.intended', route('subscription.plans'));

                return $this->redirect(route('login'), navigate: false);
            }

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
            $existingSubscriptions = $cart->items()
                ->where('cartable_type', 'Modules\Subscription\App\Models\SubscriptionPlan')
                ->get();

            foreach ($existingSubscriptions as $item) {
                $cartService->removeItem($item);
            }

            // Eski item'lar silindikten sonra toplamları sıfırla (güvenlik için)
            if ($existingSubscriptions->count() > 0) {
                $cart->refresh();
                $cart->recalculateTotals();
            }

            // Bridge ile subscription verilerini hazırla (fiyat + display info)
            $options = $bridge->prepareSubscriptionForCart($plan, $cycleKey, $autoRenew);

            // Cart'a ekle (CartService ile)
            $cartItem = $cartService->addItem($cart, $plan, 1, $options);

            // Cart refresh ve toplamları yeniden hesapla (güvenlik için)
            $cart->refresh();
            $cart->recalculateTotals();
            $itemCount = $cart->items()->where('is_active', true)->sum('quantity');

            // Events
            $this->dispatch('cartUpdated');

            \Log::info('✅ Subscription AddToCart SUCCESS', [
                'plan_id' => $planId,
                'cart_id' => $cart->cart_id,
            ]);

            // Checkout sayfasına yönlendir (cache buster ile)
            $url = route('cart.checkout') . '?t=' . time();
            return $this->redirect($url, navigate: false);

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
        // Layout: Tenant temasından (header/footer için)
        // View: Module default (içerik fallback'ten)
        $theme = tenant()->theme ?? 'simple';
        $layoutPath = "themes.{$theme}.layouts.app";

        // Tenant layout yoksa simple fallback
        if (!view()->exists($layoutPath)) {
            $layoutPath = 'themes.simple.layouts.app';
        }

        // View her zaman module default (orta kısım fallback)
        return view('subscription::livewire.front.subscription-plans', [
            'plans' => $this->plans,
            'hasEnoughSubscription' => $this->hasEnoughSubscription,
            'remainingDays' => $this->remainingDays,
            'expiresAt' => $this->expiresAt,
            'isPremium' => $this->isPremium,
            'isGuest' => $this->isGuest,
            'trialDays' => $this->trialDays,
            'userHasUsedTrial' => $this->userHasUsedTrial,
            'daysLeft' => $this->daysLeft,
        ])->layout($layoutPath);
    }
}
