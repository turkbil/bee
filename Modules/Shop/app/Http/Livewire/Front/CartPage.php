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
        $this->items = collect(); // Initialize as empty collection
        $this->loadCart();
    }

    public function loadCart()
    {
        $cartService = app(ShopCartService::class);

        $this->cart = $cartService->getCurrentCart();
        $this->items = $cartService->getItems();
        $this->itemCount = (int) ($this->cart->items_count ?? 0);

        // TRY cinsinden toplam hesapla (her item için currency check)
        $subtotalTRY = 0;
        $taxAmountTRY = 0;

        foreach ($this->items as $item) {
            $exchangeRate = 1;

            // USD veya başka currency ise TRY'ye çevir
            if ($item->currency && $item->currency->code !== 'TRY') {
                $exchangeRate = $item->currency->exchange_rate ?? 1;
            }

            $subtotalTRY += ($item->subtotal ?? 0) * $exchangeRate;
            $taxAmountTRY += ($item->tax_amount ?? 0) * $exchangeRate;
        }

        $this->subtotal = $subtotalTRY;
        $this->taxAmount = $taxAmountTRY;
        $this->total = $subtotalTRY + $taxAmountTRY;
    }

    public function updateQuantity(int $cartItemId, int $quantity)
    {
        $cartService = app(ShopCartService::class);

        try {
            $cartService->updateQuantity($cartItemId, $quantity);
            $this->loadCart();
            $this->dispatch('cartUpdated');

            // Livewire 3 dispatch syntax - parametre direkt gönder
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
        $cartService = app(ShopCartService::class);

        try {
            $cartService->removeItem($cartItemId);
            $this->loadCart();
            $this->dispatch('cartUpdated');

            $this->dispatch('cart-item-removed', message: 'Ürün sepetten çıkarıldı');
        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Hata: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        $cartService = app(ShopCartService::class);

        try {
            $cartService->clearCart();
            $this->loadCart();
            $this->dispatch('cartUpdated');

            $this->dispatch('cart-cleared', message: 'Sepet boşaltıldı');
        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Hata: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('shop::livewire.front.cart-page')
            ->layout('themes.ixtif.layouts.app');
    }
}
