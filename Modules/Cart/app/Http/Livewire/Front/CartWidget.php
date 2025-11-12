<?php

declare(strict_types=1);

namespace Modules\Cart\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;
use Modules\Cart\App\Models\Cart;

/**
 * CartWidget - Temiz, Sıfırdan Cart Sistemi
 *
 * Özellikler:
 * - Badge item count gösterimi
 * - Dropdown ile item listesi
 * - Increase/Decrease/Remove operations
 * - Alpine.js ile sync
 * - localStorage ile persistence
 */
class CartWidget extends Component
{
    // Cart state
    public ?Cart $cart = null;
    public $items = [];
    public int $itemCount = 0;
    public float $total = 0.00;

    /**
     * Livewire event listeners
     * camelCase ve kebab-case her ikisini de destekle
     */
    protected $listeners = [
        'cartUpdated' => 'loadCart',      // camelCase (eski kod uyumluluğu)
        'cart-updated' => 'loadCart',     // kebab-case (Alpine.js)
        'cart-added' => 'loadCart',       // Yeni ürün eklendiğinde
    ];

    // Component lifecycle
    public function mount()
    {
        $this->items = collect([]); // Initialize collection
        $this->loadCart();
    }

    public function hydrate()
    {
        $this->loadCart();
    }

    /**
     * Cart'ı yükle - Tek source of truth
     */
    public function loadCart(): void
    {
        try {
            $cartService = app(CartService::class);

            // Session ve user bilgisi al
            $sessionId = session()->getId();
            $customerId = auth()->check() ? auth()->id() : null;

            \Log::info('🛒 CartWidget: loadCart START', [
                'session_id' => $sessionId,
                'customer_id' => $customerId,
            ]);

            // Cart'ı bul
            $this->cart = $cartService->getCart($customerId, $sessionId);

            if ($this->cart) {
                // Items yükle (aktif olanlar)
                $this->items = $this->cart->items()
                    ->where('is_active', true)
                    ->with(['cartable'])
                    ->get();

                // Totals hesapla
                $this->itemCount = $this->items->sum('quantity');
                $this->total = (float) $this->cart->total;

                \Log::info('🛒 CartWidget: Cart loaded', [
                    'cart_id' => $this->cart->cart_id,
                    'item_count' => $this->itemCount,
                    'total' => $this->total,
                ]);
            } else {
                // Boş state
                $this->cart = null;
                $this->items = collect([]);
                $this->itemCount = 0;
                $this->total = 0.00;

                \Log::info('🛒 CartWidget: No cart found - empty state');
            }
        } catch (\Exception $e) {
            \Log::error('🛒 CartWidget: loadCart ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Error state - boş göster
            $this->cart = null;
            $this->items = collect([]);
            $this->itemCount = 0;
            $this->total = 0.00;
        }
    }

    /**
     * Item miktarını artır
     */
    public function increaseQuantity(int $cartItemId): void
    {
        try {
            if (!$this->cart) {
                return;
            }

            $item = $this->cart->items()->find($cartItemId);

            if ($item) {
                $item->quantity += 1;
                $item->recalculate();
                $this->cart->recalculateTotals();

                \Log::info('🛒 CartWidget: Quantity increased', [
                    'cart_item_id' => $cartItemId,
                    'new_quantity' => $item->quantity,
                ]);
            }

            // Reload cart state
            $this->loadCart();

            // Alpine.js event dispatch
            $this->dispatchBrowserEvent('cart-updated', [
                'cartId' => $this->cart->cart_id,
                'itemCount' => $this->itemCount,
                'total' => $this->total,
                'currencyCode' => $this->cart->currency_code ?? 'TRY',
            ]);

        } catch (\Exception $e) {
            \Log::error('🛒 CartWidget: increaseQuantity ERROR', [
                'cart_item_id' => $cartItemId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Item miktarını azalt (quantity=1 ise sil)
     */
    public function decreaseQuantity(int $cartItemId): void
    {
        try {
            if (!$this->cart) {
                return;
            }

            $item = $this->cart->items()->find($cartItemId);

            if ($item) {
                if ($item->quantity > 1) {
                    $item->quantity -= 1;
                    $item->recalculate();
                } else {
                    // Quantity=1 ise direkt sil
                    $item->delete();
                }

                $this->cart->recalculateTotals();

                \Log::info('🛒 CartWidget: Quantity decreased', [
                    'cart_item_id' => $cartItemId,
                    'new_quantity' => $item->quantity ?? 0,
                ]);
            }

            // Reload cart state
            $this->loadCart();

            // Alpine.js event dispatch
            $this->dispatchBrowserEvent('cart-updated', [
                'cartId' => $this->cart ? $this->cart->cart_id : null,
                'itemCount' => $this->itemCount,
                'total' => $this->total,
                'currencyCode' => $this->cart ? ($this->cart->currency_code ?? 'TRY') : 'TRY',
            ]);

        } catch (\Exception $e) {
            \Log::error('🛒 CartWidget: decreaseQuantity ERROR', [
                'cart_item_id' => $cartItemId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Item'ı sepetten kaldır
     */
    public function removeItem(int $cartItemId): void
    {
        try {
            if (!$this->cart) {
                return;
            }

            $item = $this->cart->items()->find($cartItemId);

            if ($item) {
                $item->delete();
                $this->cart->recalculateTotals();

                \Log::info('🛒 CartWidget: Item removed', [
                    'cart_item_id' => $cartItemId,
                ]);

                // Success notification
                $this->dispatchBrowserEvent('notify', [
                    'type' => 'success',
                    'message' => 'Ürün sepetten çıkarıldı',
                ]);
            }

            // Reload cart state
            $this->loadCart();

            // Alpine.js event dispatch
            $this->dispatchBrowserEvent('cart-updated', [
                'cartId' => $this->cart ? $this->cart->cart_id : null,
                'itemCount' => $this->itemCount,
                'total' => $this->total,
                'currencyCode' => $this->cart ? ($this->cart->currency_code ?? 'TRY') : 'TRY',
            ]);

        } catch (\Exception $e) {
            \Log::error('🛒 CartWidget: removeItem ERROR', [
                'cart_item_id' => $cartItemId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Ürün kaldırılırken hata oluştu',
            ]);
        }
    }

    public function render()
    {
        return view('cart::livewire.front.cart-widget');
    }
}
