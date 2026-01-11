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
        $cartService = app(CartService::class);

        // Session ID veya customer ID ile cart al
        $sessionId = session()->getId();
        $customerId = auth()->check() ? auth()->id() : null;

        $this->cart = $cartService->getCart($customerId, $sessionId);

        if ($this->cart) {
            // Önce item'ları al
            $this->items = $this->cart->items()->where('is_active', true)->get();
            $this->itemCount = $this->items->sum('quantity');

            // 🔥 ITEM'LARDAN DİREKT HESAPLA - Cart tablosuna güvenme!
            $this->subtotal = (float) $this->items->sum('subtotal');
            $this->taxAmount = (float) $this->items->sum('tax_amount');
            $this->total = $this->subtotal + $this->taxAmount;

            // Cart tablosunu da güncelle (senkron tut)
            $this->cart->subtotal = $this->subtotal;
            $this->cart->tax_amount = $this->taxAmount;
            $this->cart->total = $this->total;
            $this->cart->items_count = $this->itemCount;
            $this->cart->save();
        } else {
            $this->items = collect([]);
            $this->itemCount = 0;
            $this->subtotal = 0.0;
            $this->taxAmount = 0.0;
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
                // CartService kullan (consistent)
                $cartService = app(CartService::class);
                $cartService->removeItem($item);
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
        // 🔥 NO-CACHE HEADERS - Browser cache'i engelle
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

        // Layout: Tenant temasından (header/footer için)
        // View: Module default (içerik fallback'ten)
        $theme = tenant()->theme ?? 'simple';
        $layoutPath = "themes.{$theme}.layouts.app";

        // Tenant layout yoksa simple fallback
        if (!view()->exists($layoutPath)) {
            $layoutPath = 'themes.simple.layouts.app';
        }

        // View her zaman module default (orta kısım fallback)
        return view('cart::livewire.front.cart-page')
            ->layout($layoutPath);
    }
}
