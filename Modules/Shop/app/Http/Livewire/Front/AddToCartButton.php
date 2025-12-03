<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Services\ShopCartBridge;

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

    // UI Properties
    public bool $showQuantity = false;
    public string $buttonText = 'Sepete Ekle';
    public string $buttonClass = 'px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors';

    public function mount(
        int $productId,
        int $quantity = 1,
        bool $showQuantity = false,
        string $buttonText = 'Sepete Ekle',
        string $buttonClass = 'px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors'
    )
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->showQuantity = $showQuantity;
        $this->buttonText = $buttonText;
        $this->buttonClass = $buttonClass;
    }

    /**
     * Quantity artır
     */
    public function increaseQuantity()
    {
        $this->quantity++;
    }

    /**
     * Quantity azalt
     */
    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /**
     * Sepete ekle - EN BASIT HAL
     */
    public function addToCart()
    {
        $this->isAdding = true;

        // 🎯 Cart icon animasyonu için optimistic event
        $this->js('window.dispatchEvent(new CustomEvent("optimistic-add", { detail: { quantity: ' . $this->quantity . ' } }))');

        try {
            // Cart service
            $cartService = app(CartService::class);
            $bridge = app(ShopCartBridge::class);

            // Ürünü al
            $product = ShopProduct::findOrFail($this->productId);

            // Stok kontrolü
            if (!$bridge->canAddToCart($product, $this->quantity)) {
                $errors = $bridge->getCartItemErrors($product, $this->quantity);
                throw new \Exception(implode(' ', $errors));
            }

            // Session/Customer
            $sessionId = session()->getId();
            $customerId = auth()->check() ? auth()->id() : null;

            // Cart bul/oluştur
            $cart = $cartService->findOrCreateCart($customerId, $sessionId);

            // Display bilgileri ve currency hazırla (Bridge service kullan)
            $options = $bridge->prepareProductForCart($product, $this->quantity);

            // Ürünü ekle
            $cartItem = $cartService->addItem($cart, $product, $this->quantity, $options);

            // Cart'ı refresh et
            $cart->refresh();

            // Item count hesapla
            $itemCount = $cart->items()->where('is_active', true)->sum('quantity');

            // 1️⃣ Global Livewire event → CartWidget refresh için
            $this->dispatch('cartUpdated');

            // 2️⃣ Browser window event → Success notification için (CartPage dinliyor)
            $this->js(sprintf(
                'window.dispatchEvent(new CustomEvent("cart-updated", { detail: %s }))',
                json_encode([
                    'cartId' => $cart->cart_id,
                    'itemCount' => $itemCount,
                    'message' => 'Ürün sepete eklendi!',
                    'productName' => $product->getTranslated('title', app()->getLocale()),
                    'productPrice' => $cartItem->unit_price,
                    'quantity' => $cartItem->quantity,
                ])
            ));

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

            // Browser window event → Error notification için
            $this->js(sprintf(
                'window.dispatchEvent(new CustomEvent("cart-error", { detail: %s }))',
                json_encode([
                    'message' => 'Hata: ' . $e->getMessage(),
                ])
            ));
        } finally {
            $this->isAdding = false;
        }
    }

    public function render()
    {
        return view('shop::livewire.front.add-to-cart-button');
    }
}
