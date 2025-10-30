<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Shop\App\Services\ShopCartService;

class CartPage extends Component
{
    public $cart;
    public $items = [];
    public int $itemCount = 0;
    public float $subtotal = 0;
    public float $taxAmount = 0;
    public float $total = 0;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $cartService = app(ShopCartService::class);

        $this->cart = $cartService->getCurrentCart();
        $this->items = $cartService->getItems();
        $this->itemCount = $this->cart->items_count;
        $this->subtotal = (float) $this->cart->subtotal;
        $this->taxAmount = (float) $this->cart->tax_amount;
        $this->total = (float) $this->cart->total;
    }

    public function updateQuantity(int $cartItemId, int $quantity)
    {
        $cartService = app(ShopCartService::class);

        try {
            $cartService->updateQuantity($cartItemId, $quantity);
            $this->loadCart();
            $this->emit('cartUpdated');

            $this->dispatchBrowserEvent('cart-updated', [
                'message' => 'Sepet güncellendi',
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('cart-error', [
                'message' => 'Hata: ' . $e->getMessage(),
            ]);
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
        $cartService = app(ShopCartService::class);

        try {
            $cartService->removeItem($cartItemId);
            $this->loadCart();
            $this->emit('cartUpdated');

            $this->dispatchBrowserEvent('cart-item-removed', [
                'message' => 'Ürün sepetten çıkarıldı',
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('cart-error', [
                'message' => 'Hata: ' . $e->getMessage(),
            ]);
        }
    }

    public function clearCart()
    {
        $cartService = app(ShopCartService::class);

        try {
            $cartService->clearCart();
            $this->loadCart();
            $this->emit('cartUpdated');

            $this->dispatchBrowserEvent('cart-cleared', [
                'message' => 'Sepet boşaltıldı',
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('cart-error', [
                'message' => 'Hata: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('shop::livewire.front.cart-page')
            ->layout('themes.ixtif.layouts.app');
    }
}
