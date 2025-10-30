<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Shop\App\Services\ShopCartService;

class CartWidget extends Component
{
    public int $itemCount = 0;
    public float $total = 0;
    public $items = [];
    public $cart = null;
    public string $currencySymbol = '₺';
    public string $formattedTotal = '0,00 ₺';

    protected $listeners = ['cartUpdated' => 'refreshCart'];

    public function mount()
    {
        $this->refreshCart();
    }

    public function refreshCart()
    {
        $cartService = app(ShopCartService::class);

        $this->cart = $cartService->getCurrentCart();
        $this->itemCount = $cartService->getItemCount();
        $this->total = $cartService->getTotal();
        $this->items = $cartService->getItems();

        // Currency bilgisini al
        $cartCurrency = $this->cart->relationLoaded('currency') ? $this->cart->getRelation('currency') : null;

        if ($cartCurrency) {
            $this->currencySymbol = $cartCurrency->symbol;
            $this->formattedTotal = $cartCurrency->formatPrice($this->total);
        } else {
            $this->currencySymbol = '₺';
            $this->formattedTotal = number_format($this->total, 2, ',', '.') . ' ₺';
        }
    }

    public function removeItem(int $cartItemId)
    {
        $cartService = app(ShopCartService::class);
        $cartService->removeItem($cartItemId);

        $this->refreshCart();
        $this->dispatch('cartUpdated');

        $this->dispatch('cart-item-removed', [
            'message' => 'Ürün sepetten çıkarıldı',
        ]);
    }

    public function increaseQuantity(int $cartItemId)
    {
        $cartService = app(ShopCartService::class);
        $item = $cartService->getItems()->firstWhere('cart_item_id', $cartItemId);

        if ($item) {
            $cartService->updateQuantity($cartItemId, $item->quantity + 1);
        }

        $this->refreshCart();
        $this->dispatch('cartUpdated');
    }

    public function decreaseQuantity(int $cartItemId)
    {
        $cartService = app(ShopCartService::class);
        $item = $cartService->getItems()->firstWhere('cart_item_id', $cartItemId);

        if ($item && $item->quantity > 1) {
            $cartService->updateQuantity($cartItemId, $item->quantity - 1);
        } elseif ($item && $item->quantity === 1) {
            // Miktar 1 ise, sil
            $cartService->removeItem($cartItemId);
        }

        $this->refreshCart();
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('shop::livewire.front.cart-widget');
    }
}
