<?php

declare(strict_types=1);

namespace Modules\Cart\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;

class CartWidget extends Component
{
    public int $itemCount = 0;
    public float $total = 0;
    public $items = [];
    public $cart = null;

    protected $listeners = ['cartUpdated' => 'refreshCart'];

    public function mount()
    {
        $this->items = collect([]); // Initialize collection
        $this->refreshCart();
    }

    public function hydrate()
    {
        $this->refreshCart();
    }

    public function refreshCart($cartId = null)
    {
        \Log::info('🔄 CartWidget: refreshCart START', ['cart_id_param' => $cartId]);

        $cartService = app(CartService::class);
        $sessionId = session()->getId();
        $customerId = auth()->check() ? auth()->id() : null;

        // Önce parametre olarak gelen cart_id'yi kontrol et (Alpine.js'den gelecek)
        if ($cartId) {
            // cart_id varsa direkt cart'ı bul
            $this->cart = \Modules\Cart\App\Models\Cart::find($cartId);
            \Log::info('🔄 CartWidget: Cart loaded by ID', [
                'cart_id' => $cartId,
                'found' => $this->cart ? 'yes' : 'no',
            ]);

            // Cart bulunamadıysa (geçersiz localStorage cart_id)
            if (!$this->cart) {
                \Log::warning('⚠️ CartWidget: Invalid cart_id from localStorage, trying session', [
                    'invalid_cart_id' => $cartId,
                    'session_id' => $sessionId,
                ]);

                // Session ile doğru cart'ı bul
                $this->cart = $cartService->getCart($customerId, $sessionId);

                // Doğru cart_id'yi frontend'e gönder (localStorage güncellensin)
                if ($this->cart) {
                    $this->dispatchBrowserEvent('cart-id-corrected', [
                        'cartId' => $this->cart->cart_id,
                        'message' => 'localStorage cart_id düzeltildi',
                    ]);
                }
            }
        } else {
            // cart_id yoksa session/customer ile bul
            \Log::info('🔄 CartWidget: Getting cart by session', [
                'session_id' => $sessionId,
                'customer_id' => $customerId,
            ]);

            $this->cart = $cartService->getCart($customerId, $sessionId);
        }

        if ($this->cart) {
            // Eager load cartable relation (polymorphic)
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
                'items' => $this->items->count(),
            ]);
        } else {
            $this->items = collect([]);
            $this->itemCount = 0;
            $this->total = 0;

            \Log::info('🔄 CartWidget: No cart found - empty state');
        }
    }

    public function removeItem(int $cartItemId)
    {
        // Cart yoksa veya geçersizse işlem yapma
        if (!$this->cart) {
            \Log::warning('⚠️ CartWidget: removeItem called but cart is null', [
                'cart_item_id' => $cartItemId,
            ]);
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            $item->delete();
            $this->cart->recalculateTotals();

            // Items ve count'u manuel güncelle (refreshCart çağırma!)
            $this->items = $this->cart->items()
                ->where('is_active', true)
                ->with(['cartable'])
                ->get();
            $this->itemCount = $this->items->sum('quantity');
            $this->total = (float) $this->cart->total;
        }

        // Browser event gönder (Alpine.js badge'ini güncelle)
        $this->dispatchBrowserEvent('cart-updated', [
            'cartId' => $this->cart->cart_id,
            'itemCount' => $this->itemCount,
        ]);
    }

    public function increaseQuantity(int $cartItemId)
    {
        // Cart yoksa veya geçersizse işlem yapma
        if (!$this->cart) {
            \Log::warning('⚠️ CartWidget: increaseQuantity called but cart is null', [
                'cart_item_id' => $cartItemId,
            ]);
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            $item->quantity += 1;
            $item->recalculate();
            $this->cart->recalculateTotals();

            // Items ve count'u manuel güncelle (refreshCart çağırma!)
            $this->items = $this->cart->items()
                ->where('is_active', true)
                ->with(['cartable'])
                ->get();
            $this->itemCount = $this->items->sum('quantity');
            $this->total = (float) $this->cart->total;
        }

        // Browser event gönder (Alpine.js badge'ini güncelle)
        $this->dispatchBrowserEvent('cart-updated', [
            'cartId' => $this->cart->cart_id,
            'itemCount' => $this->itemCount,
        ]);
    }

    public function decreaseQuantity(int $cartItemId)
    {
        // Cart yoksa veya geçersizse işlem yapma
        if (!$this->cart) {
            \Log::warning('⚠️ CartWidget: decreaseQuantity called but cart is null', [
                'cart_item_id' => $cartItemId,
            ]);
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            if ($item->quantity > 1) {
                $item->quantity -= 1;
                $item->recalculate();
            } else {
                // Quantity 1 iken - yapınca removeItem gibi çalış
                $item->delete();
            }
            $this->cart->recalculateTotals();

            // Items ve count'u manuel güncelle (refreshCart çağırma!)
            $this->items = $this->cart->items()
                ->where('is_active', true)
                ->with(['cartable'])
                ->get();
            $this->itemCount = $this->items->sum('quantity');
            $this->total = (float) $this->cart->total;
        }

        // Browser event gönder (Alpine.js badge'ini güncelle)
        $this->dispatchBrowserEvent('cart-updated', [
            'cartId' => $this->cart->cart_id,
            'itemCount' => $this->itemCount,
        ]);
    }

    public function render()
    {
        return view('cart::livewire.front.cart-widget');
    }
}
