<?php

declare(strict_types=1);

namespace Modules\Cart\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;

class CartPage extends Component
{
    public $cart;
    public $items = [];
    public int $itemCount = 0;
    public float $subtotal = 0;
    public float $total = 0;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->items = collect(); // Initialize as empty collection
        $this->loadCart();
    }

    public function loadCart()
    {
        $cartService = app(CartService::class);

        // Session ID veya customer ID ile cart al
        $sessionId = session()->getId();
        $customerId = auth()->check() ? auth()->id() : null;

        $this->cart = $cartService->getCart($customerId, $sessionId);

        if ($this->cart) {
            $this->items = $this->cart->items()->where('is_active', true)->get();
            $this->itemCount = $this->items->sum('quantity');
            $this->subtotal = (float) $this->cart->subtotal;
            $this->total = (float) $this->cart->total;
        } else {
            $this->items = collect([]);
            $this->itemCount = 0;
            $this->subtotal = 0.0;
            $this->total = 0.0;
        }
    }

    public function updateQuantity(int $cartItemId, int $quantity)
    {
        if (!$this->cart) {
            return;
        }

        try {
            $item = $this->cart->items()->find($cartItemId);

            if ($item) {
                $item->quantity = $quantity;
                $item->recalculate();
                $this->cart->recalculateTotals();
            }

            $this->loadCart();
            $this->dispatch('cartUpdated');
            $this->dispatch('cart-updated', message: 'Sepet güncellendi');
        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Hata: ' . $e->getMessage());
        }
    }

    public function increaseQuantity(int $cartItemId)
    {
        $item = $this->items->firstWhere('cart_item_id', $cartItemId);
        if ($item) {
            $this->updateQuantity($cartItemId, $item->quantity + 1);
        }
    }

    public function decreaseQuantity(int $cartItemId)
    {
        $item = $this->items->firstWhere('cart_item_id', $cartItemId);
        if ($item && $item->quantity > 1) {
            $this->updateQuantity($cartItemId, $item->quantity - 1);
        }
    }

    public function removeItem(int $cartItemId)
    {
        if (!$this->cart) {
            return;
        }

        try {
            $item = $this->cart->items()->find($cartItemId);

            if ($item) {
                $item->delete();
                $this->cart->recalculate();
            }

            $this->loadCart();
            $this->dispatch('cartUpdated');
            $this->dispatch('cart-item-removed', message: 'Ürün sepetten çıkarıldı');
        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Hata: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        if (!$this->cart) {
            return;
        }

        try {
            $cartService = app(CartService::class);
            $cartService->clearCart($this->cart);

            $this->loadCart();
            $this->dispatch('cartUpdated');
            $this->dispatch('cart-cleared', message: 'Sepet boşaltıldı');
        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Hata: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('cart::livewire.front.cart-page')
            ->layout('themes.ixtif.layouts.app');
    }
}
