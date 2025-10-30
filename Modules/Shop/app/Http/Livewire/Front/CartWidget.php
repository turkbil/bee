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

    /**
     * Her sayfa yüklendiğinde/değiştiğinde çalışır
     */
    public function hydrate()
    {
        $this->refreshCart();
    }

    public function refreshCart()
    {
        $cartService = app(ShopCartService::class);

        $this->cart = $cartService->getCurrentCart();
        $this->itemCount = $cartService->getItemCount();

        // Tüm item'ları TRY'ye çevir ve toplamı hesapla
        $this->items = $cartService->getItems()->map(function ($item) {
            // USD ise TRY'ye çevir
            if ($item->currency && $item->currency->code !== 'TRY') {
                $exchangeRate = $item->currency->exchange_rate;
                $item->final_price_try = $item->final_price * $exchangeRate;
                $item->subtotal_try = $item->subtotal * $exchangeRate;
                $item->tax_amount_try = $item->tax_amount * $exchangeRate;
                $item->total_try = $item->total * $exchangeRate;
            } else {
                // TRY ise olduğu gibi
                $item->final_price_try = $item->final_price;
                $item->subtotal_try = $item->subtotal;
                $item->tax_amount_try = $item->tax_amount;
                $item->total_try = $item->total;
            }

            return $item;
        });

        // Toplam KDV dahil TRY
        $this->total = $this->items->sum('total_try');
        $this->currencySymbol = '₺';
        $this->formattedTotal = number_format($this->total, 0, ',', '.') . ' ₺';
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

    /**
     * Sepete ürün ekle
     */
    public function addToCart(int $productId, int $quantity = 1)
    {
        try {
            $cartService = app(ShopCartService::class);
            $cartService->addItem($productId, $quantity);

            $this->refreshCart();
            $this->dispatch('cartUpdated');

            $this->dispatch('product-added-to-cart', [
                'message' => 'Ürün sepete eklendi',
            ]);

        } catch (\Exception $e) {
            \Log::error('Add to cart failed: ' . $e->getMessage());

            $this->dispatch('cart-error', [
                'message' => 'Ürün sepete eklenirken hata oluştu',
            ]);
        }
    }

    public function render()
    {
        return view('shop::livewire.front.cart-widget');
    }
}
