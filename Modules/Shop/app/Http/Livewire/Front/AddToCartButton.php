<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;
use Modules\Shop\App\Models\ShopProduct;

/**
 * AddToCartButton - SIFIRDAN BASIT
 *
 * Sadece: Ürün ekle → Event gönder
 */
class AddToCartButton extends Component
{
    public int $productId;
    public int $quantity = 1;
    public bool $isAdding = false;

    public function mount(int $productId, int $quantity = 1)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * Sepete ekle - EN BASIT HAL
     */
    public function addToCart()
    {
        $this->isAdding = true;

        try {
            // Cart service
            $cartService = app(CartService::class);

            // Ürünü al
            $product = ShopProduct::findOrFail($this->productId);

            // Session/Customer
            $sessionId = session()->getId();
            $customerId = auth()->check() ? auth()->id() : null;

            // Cart bul/oluştur
            $cart = $cartService->findOrCreateCart($customerId, $sessionId);

            // Ürünü ekle
            $cartItem = $cartService->addItem($cart, $product, $this->quantity, []);

            // Cart'ı refresh et
            $cart->refresh();

            // Item count hesapla
            $itemCount = $cart->items()->where('is_active', true)->sum('quantity');

            // Browser event gönder (Alpine.js yakalayacak)
            $this->dispatchBrowserEvent('cart-item-added', [
                'cartId' => $cart->cart_id,
                'itemCount' => $itemCount,
                'cartItemId' => $cartItem->cart_item_id,
                'productName' => $product->getTranslated('title', app()->getLocale()),
                'productImage' => $cartItem->item_image,
                'productPrice' => $cartItem->unit_price,
                'quantity' => $cartItem->quantity,
            ]);

            \Log::info('✅ AddToCart SUCCESS', [
                'product_id' => $this->productId,
                'cart_id' => $cart->cart_id,
                'item_count' => $itemCount,
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ AddToCart ERROR', [
                'product_id' => $this->productId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Hata: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isAdding = false;
        }
    }

    public function render()
    {
        return view('shop::livewire.front.add-to-cart-button');
    }
}
