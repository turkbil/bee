<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;
use Modules\Shop\App\Models\ShopProduct;

class AddToCartButton extends Component
{
    public int $productId;
    public ?int $variantId = null;
    public int $quantity = 1;
    public string $buttonText = 'Sepete Ekle';
    public string $buttonClass = 'btn btn-primary';
    public bool $showQuantity = false;
    public bool $isAdding = false;

    protected $rules = [
        'quantity' => 'required|integer|min:1',
    ];

    public function mount(
        int $productId,
        ?int $variantId = null,
        int $quantity = 1,
        string $buttonText = 'Sepete Ekle',
        string $buttonClass = 'btn btn-primary',
        bool $showQuantity = false
    ) {
        $this->productId = $productId;
        $this->variantId = $variantId;
        $this->quantity = $quantity;
        $this->buttonText = $buttonText;
        $this->buttonClass = $buttonClass;
        $this->showQuantity = $showQuantity;
    }

    public function addToCart()
    {
        \Log::info('🛒 AddToCart: START', [
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'variant_id' => $this->variantId,
        ]);

        $this->validate();

        $this->isAdding = true;

        try {
            // Cart modülünün polymorphic CartService'ini kullan
            $cartService = app(CartService::class);
            \Log::info('🛒 AddToCart: CartService initialized');

            // Ürünü al
            $product = ShopProduct::findOrFail($this->productId);
            \Log::info('🛒 AddToCart: Product loaded', [
                'product_id' => $product->product_id,
                'sku' => $product->sku,
                'base_price' => $product->base_price,
                'final_price' => $product->final_price ?? 'NULL',
            ]);

            // Session ve customer bilgisi
            $sessionId = session()->getId();
            $customerId = auth()->check() ? auth()->id() : null;
            \Log::info('🛒 AddToCart: Session info', [
                'session_id' => $sessionId,
                'customer_id' => $customerId,
            ]);

            // Sepeti al veya oluştur
            $cart = $cartService->findOrCreateCart($customerId, $sessionId);
            \Log::info('🛒 AddToCart: Cart loaded/created', [
                'cart_id' => $cart->cart_id,
                'status' => $cart->status,
            ]);

            // Polymorphic olarak ürünü ekle
            $options = [];
            if ($this->variantId) {
                $options['customization_options'] = ['variant_id' => $this->variantId];
            }

            $cartItem = $cartService->addItem($cart, $product, $this->quantity, $options);
            \Log::info('🛒 AddToCart: Item added to cart', [
                'cart_item_id' => $cartItem->cart_item_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
                'total' => $cartItem->total,
            ]);

            // Sepet güncel bilgilerini al
            $cart->refresh();
            $itemCount = $cart->items()->where('is_active', true)->sum('quantity');

            // Alpine.js uyumlu event gönder (kebab-case)
            $this->dispatch('cart-updated', [
                'cartId' => $cart->cart_id,
                'itemCount' => $itemCount,
                'total' => (float) $cart->total,
                'currencyCode' => $cart->currency_code ?? 'TRY',
            ]);
            \Log::info('🛒 AddToCart: cart-updated event dispatched (Alpine.js)', [
                'cart_id' => $cart->cart_id,
                'item_count' => $itemCount,
            ]);

            $this->dispatch('product-added-to-cart', [
                'message' => 'Ürün sepete eklendi!',
                'productId' => $this->productId,
                'cartId' => $cart->cart_id,
            ]);

            // Quantity'yi reset et
            if (!$this->showQuantity) {
                $this->quantity = 1;
            }

            \Log::info('🛒 AddToCart: SUCCESS - Cart updated');
        } catch (\Exception $e) {
            \Log::error('🛒 AddToCart: ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('cart-error', [
                'message' => 'Hata: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isAdding = false;
        }
    }

    public function increaseQuantity()
    {
        $this->quantity++;
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        return view('shop::livewire.front.add-to-cart-button');
    }
}
