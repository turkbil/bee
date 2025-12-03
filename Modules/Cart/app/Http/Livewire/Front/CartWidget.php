<?php

declare(strict_types=1);

namespace Modules\Cart\App\Http\Livewire\Front;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Cart\App\Services\CartService;

class CartWidget extends Component
{
    public int $itemCount = 0;
    public float $total = 0;
    public float $subtotal = 0;
    public float $taxAmount = 0;
    public array $items = [];
    public ?int $cartId = null;

    public function mount()
    {
        // 🔐 Kullanıcı login ise customer_id ile cart yükle
        if (auth()->check()) {
            $customerId = auth()->id();
            $cartService = app(CartService::class);

            // Kullanıcının aktif cart'ını bul
            $cart = \Modules\Cart\App\Models\Cart::where('customer_id', $customerId)
                ->where('status', 'active')
                ->first();

            if ($cart) {
                $this->loadCartData($cart);
                \Log::info('🔐 CartWidget: Loaded user cart on mount', [
                    'customer_id' => $customerId,
                    'cart_id' => $cart->cart_id,
                ]);
                return;
            }
        }

        // Guest kullanıcı - localStorage'dan yüklenecek (@script'te)
        $this->items = [];
        $this->itemCount = 0;
        $this->subtotal = 0.0;
        $this->taxAmount = 0.0;
        $this->total = 0.0;
        $this->cartId = null;
    }

    /**
     * Cart ID ile refresh (localStorage'dan)
     */
    public function refreshCartById($cartId)
    {
        \Log::info('🔄 CartWidget: refreshCartById called', ['cart_id' => $cartId]);

        // Tenant context yoksa sorgu yapma (central DB'ye gitmesini engelle)
        if (!function_exists('tenant') || !tenant()) {
            \Log::warning('CartWidget: Tenant context not initialized, skipping cart query');
            return;
        }

        $cart = \Modules\Cart\App\Models\Cart::where('cart_id', $cartId)
            ->where('status', 'active')
            ->first();

        $this->loadCartData($cart);
    }

    #[On('cartUpdated')]
    public function refreshCart($cartId = null, $itemCount = null)
    {
        \Log::info('🔄 CartWidget: refreshCart called', ['cart_id' => $cartId, 'item_count' => $itemCount]);

        // Event'ten cart_id gelirse direkt onu kullan (performanslı)
        if ($cartId) {
            $this->refreshCartById($cartId);
            return;
        }

        // Fallback: localStorage'dan cart_id al
        $localCartId = request()->cookie('cart_id') ?? null;
        if ($localCartId) {
            \Log::info('🔄 CartWidget: Using cart_id from cookie', ['cart_id' => $localCartId]);
            $this->refreshCartById(intval($localCartId));
            return;
        }

        // Son çare: Session ile cart bul
        $cartService = app(CartService::class);
        $sessionId = session()->getId();
        $customerId = auth()->check() ? auth()->id() : null;

        $cart = $cartService->getCart($customerId, $sessionId);
        $this->loadCartData($cart);
    }

    /**
     * Optimistic Update - Badge'i anında artır (API beklemeden)
     */
    #[On('optimisticAdd')]
    public function optimisticAdd($quantity = 1)
    {
        \Log::info('⚡ CartWidget: Optimistic Add', ['quantity' => $quantity]);

        // Badge'i anında artır
        $this->itemCount += $quantity;
    }

    /**
     * Optimistic Update Rollback - API fail olursa geri al
     */
    #[On('optimisticRollback')]
    public function optimisticRollback($quantity = 1)
    {
        \Log::info('🔄 CartWidget: Optimistic Rollback', ['quantity' => $quantity]);

        // Badge'i geri al
        $this->itemCount = max(0, $this->itemCount - $quantity);
    }

    /**
     * Cart verilerini yükle
     */
    protected function loadCartData($cart)
    {
        if ($cart) {
            // Cart'ı yenile (database'den güncel veri)
            $cart->refresh();

            $this->cartId = $cart->cart_id;

            // Items yükle - Livewire serialization için array'e çevir
            $items = $cart->items()
                ->where('is_active', true)
                ->get();

            $this->items = $items->map(function ($item) {
                return [
                    'cart_item_id' => $item->cart_item_id,
                    'item_name' => $item->item_title ?? $item->item_name,
                    'item_image' => $item->item_image,
                    'unit_price' => (float) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'tax_rate' => (float) ($item->tax_rate ?? 0),
                    'tax_amount' => (float) ($item->tax_amount ?? 0),
                    'total' => (float) ($item->total ?? $item->subtotal),
                    'cartable_type' => $item->cartable_type, // Subscription kontrolü için
                ];
            })->toArray();

            $this->itemCount = $items->sum('quantity');
            $this->subtotal = (float) $cart->subtotal;
            $this->taxAmount = (float) $cart->tax_amount;
            $this->total = (float) $cart->total;

            \Log::info('🔄 CartWidget: Cart loaded', [
                'cart_id' => $cart->cart_id,
                'item_count' => $this->itemCount,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'total' => $this->total,
            ]);
        } else {
            $this->cartId = null;
            $this->items = [];
            $this->itemCount = 0;
            $this->subtotal = 0.0;
            $this->taxAmount = 0.0;
            $this->total = 0.0;

            \Log::warning('⚠️ CartWidget: No cart found');
        }
    }

    public function removeItem(int $cartItemId)
    {
        \Log::info('🗑️ CartWidget: removeItem', ['cart_item_id' => $cartItemId]);

        if (!$this->cartId) {
            \Log::warning('⚠️ CartWidget: No cart');
            return;
        }

        $cart = \Modules\Cart\App\Models\Cart::find($this->cartId);
        if (!$cart) return;

        $item = $cart->items()->find($cartItemId);

        if ($item) {
            $item->delete();
            $cart->recalculateTotals();
            $this->loadCartData($cart);

            // 🔄 CartPage'i güncelle
            $this->dispatch('cartUpdated');

            \Log::info('✅ CartWidget: Item removed');
        }
    }

    public function increaseQuantity(int $cartItemId)
    {
        \Log::info('➕ CartWidget: increaseQuantity', ['cart_item_id' => $cartItemId]);

        if (!$this->cartId) {
            \Log::warning('⚠️ CartWidget: No cart');
            return;
        }

        $cart = \Modules\Cart\App\Models\Cart::find($this->cartId);
        if (!$cart) return;

        $item = $cart->items()->find($cartItemId);

        if ($item) {
            $item->quantity += 1;
            $item->recalculate();
            $cart->recalculateTotals();
            $this->loadCartData($cart);

            // 🔄 CartPage'i güncelle
            $this->dispatch('cartUpdated');

            \Log::info('✅ CartWidget: Quantity increased', ['new_qty' => $item->quantity]);
        }
    }

    public function decreaseQuantity(int $cartItemId)
    {
        \Log::info('➖ CartWidget: decreaseQuantity', ['cart_item_id' => $cartItemId]);

        if (!$this->cartId) {
            \Log::warning('⚠️ CartWidget: No cart');
            return;
        }

        $cart = \Modules\Cart\App\Models\Cart::find($this->cartId);
        if (!$cart) return;

        $item = $cart->items()->find($cartItemId);

        if ($item) {
            if ($item->quantity > 1) {
                $item->quantity -= 1;
                $item->recalculate();
            } else {
                // Quantity 1 iken - = removeItem
                $item->delete();
            }
            $cart->recalculateTotals();
            $this->loadCartData($cart);

            // 🔄 CartPage'i güncelle
            $this->dispatch('cartUpdated');

            \Log::info('✅ CartWidget: Quantity decreased');
        }
    }

    public function render()
    {
        return view('cart::livewire.front.cart-widget');
    }
}
