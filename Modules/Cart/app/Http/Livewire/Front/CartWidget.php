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
    public $items = [];
    public $cart = null;

    public function mount()
    {
        $this->refreshCart();
    }

    #[On('cartUpdated')]
    public function refreshCart()
    {
        \Log::info('🔄 CartWidget: refreshCart called');

        $cartService = app(CartService::class);
        $sessionId = session()->getId();
        $customerId = auth()->check() ? auth()->id() : null;

        // Session ile cart bul/oluştur
        $this->cart = $cartService->findOrCreateCart($customerId, $sessionId);

        if ($this->cart) {
            // Items yükle
            $this->items = $this->cart->items()
                ->where('is_active', true)
                ->with(['cartable'])
                ->get();

            $this->itemCount = $this->items->sum('quantity');
            $this->total = (float) $this->cart->total;

            \Log::info('🔄 CartWidget: Cart loaded', [
                'cart_id' => $this->cart->cart_id,
                'item_count' => $this->itemCount,
                'total' => $this->total,
            ]);
        } else {
            $this->items = collect([]);
            $this->itemCount = 0;
            $this->total = 0.0;

            \Log::warning('⚠️ CartWidget: No cart found');
        }
    }

    public function removeItem(int $cartItemId)
    {
        \Log::info('🗑️ CartWidget: removeItem', ['cart_item_id' => $cartItemId]);

        if (!$this->cart) {
            \Log::warning('⚠️ CartWidget: No cart');
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            $item->delete();
            $this->cart->recalculateTotals();
            $this->refreshCart();

            \Log::info('✅ CartWidget: Item removed');
        }
    }

    public function increaseQuantity(int $cartItemId)
    {
        \Log::info('➕ CartWidget: increaseQuantity', ['cart_item_id' => $cartItemId]);

        if (!$this->cart) {
            \Log::warning('⚠️ CartWidget: No cart');
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            $item->quantity += 1;
            $item->recalculate();
            $this->cart->recalculateTotals();
            $this->refreshCart();

            \Log::info('✅ CartWidget: Quantity increased', ['new_qty' => $item->quantity]);
        }
    }

    public function decreaseQuantity(int $cartItemId)
    {
        \Log::info('➖ CartWidget: decreaseQuantity', ['cart_item_id' => $cartItemId]);

        if (!$this->cart) {
            \Log::warning('⚠️ CartWidget: No cart');
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            if ($item->quantity > 1) {
                $item->quantity -= 1;
                $item->recalculate();
            } else {
                // Quantity 1 iken - = removeItem
                $item->delete();
            }
            $this->cart->recalculateTotals();
            $this->refreshCart();

            \Log::info('✅ CartWidget: Quantity decreased');
        }
    }

    public function render()
    {
        return view('cart::livewire.front.cart-widget');
    }
}
