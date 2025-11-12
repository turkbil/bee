<?php

declare(strict_types=1);

namespace Modules\Cart\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;

/**
 * CartWidget - SIFIRDAN BASIT
 *
 * Sadece: Badge göster + Dropdown'da item'ları listele
 */
class CartWidget extends Component
{
    // State
    public int $itemCount = 0;
    public array $items = [];

    public function mount()
    {
        $this->loadCart();
    }

    /**
     * Cart'ı yükle - EN BASIT HAL
     */
    public function loadCart(): void
    {
        try {
            $cartService = app(CartService::class);

            // Session/Customer
            $sessionId = session()->getId();
            $customerId = auth()->check() ? auth()->id() : null;

            // Cart bul
            $cart = $cartService->getCart($customerId, $sessionId);

            if ($cart) {
                // Items al
                $cartItems = $cart->items()->where('is_active', true)->get();

                // Item count hesapla
                $this->itemCount = $cartItems->sum('quantity');

                // Items'ı array'e çevir (Livewire için)
                $this->items = $cartItems->map(function ($item) {
                    return [
                        'cart_item_id' => $item->cart_item_id,
                        'name' => $item->item_name,
                        'image' => $item->item_image,
                        'price' => $item->unit_price,
                        'quantity' => $item->quantity,
                    ];
                })->toArray();

                \Log::info('✅ CartWidget: Loaded', [
                    'item_count' => $this->itemCount,
                    'items' => count($this->items),
                ]);
            } else {
                // Boş state
                $this->itemCount = 0;
                $this->items = [];

                \Log::info('ℹ️ CartWidget: Empty cart');
            }

        } catch (\Exception $e) {
            \Log::error('❌ CartWidget: Load error', [
                'error' => $e->getMessage(),
            ]);

            // Error state - boş göster
            $this->itemCount = 0;
            $this->items = [];
        }
    }

    public function render()
    {
        return view('cart::livewire.front.cart-widget');
    }
}
