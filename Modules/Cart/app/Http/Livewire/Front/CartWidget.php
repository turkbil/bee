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

    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'cart-updated' => 'refreshCart',
    ];

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

        // Önce parametre olarak gelen cart_id'yi kontrol et (Alpine.js'den gelecek)
        if ($cartId) {
            // cart_id varsa direkt cart'ı bul
            $this->cart = \Modules\Cart\App\Models\Cart::find($cartId);
            \Log::info('🔄 CartWidget: Cart loaded by ID', [
                'cart_id' => $cartId,
                'found' => $this->cart ? 'yes' : 'no',
            ]);
        } else {
            // cart_id yoksa session/customer ile bul
            $sessionId = session()->getId();
            $customerId = auth()->check() ? auth()->id() : null;

            \Log::info('🔄 CartWidget: Getting cart by session', [
                'session_id' => $sessionId,
                'customer_id' => $customerId,
            ]);

            $this->cart = $cartService->getCart($customerId, $sessionId);
        }

        if ($this->cart) {
            // Eager load polymorphic relations and product details
            $this->items = $this->cart->items()
                ->where('is_active', true)
                ->with([
                    'cartable',
                    'product' => function ($query) {
                        $query->select('product_id', 'title', 'slug');
                    }
                ])
                ->get();

            // Manually eager load medias if relation exists (safe - try/catch)
            if ($this->items->isNotEmpty()) {
                try {
                    $productIds = $this->items->pluck('product_id')->filter()->unique();
                    if ($productIds->isNotEmpty()) {
                        // Try to load with medias, fallback to without medias if relation doesn't exist
                        $query = \Modules\Shop\App\Models\ShopProduct::whereIn('product_id', $productIds);

                        // Check if medias relation exists before using it
                        if (method_exists(\Modules\Shop\App\Models\ShopProduct::class, 'medias')) {
                            $query->with('medias');
                        }

                        $products = $query->get()->keyBy('product_id');

                        // Attach products to items
                        foreach ($this->items as $item) {
                            if ($item->product_id && isset($products[$item->product_id])) {
                                $item->setRelation('product', $products[$item->product_id]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('CartWidget: Error loading product medias', [
                        'error' => $e->getMessage()
                    ]);
                    // Continue without medias - accessor will handle fallback
                }
            }

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
        if (!$this->cart) {
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            $item->delete();
            $this->cart->recalculate();
        }

        $this->refreshCart();

        // Alpine.js uyumlu event dispatch (kebab-case)
        $this->dispatch('cart-updated', [
            'cartId' => $this->cart->cart_id,
            'itemCount' => $this->itemCount,
            'total' => $this->total,
            'currencyCode' => $this->cart->currency_code ?? 'TRY',
        ]);

        $this->dispatch('cart-item-removed', ['message' => 'Ürün sepetten çıkarıldı']);
    }

    public function increaseQuantity(int $cartItemId)
    {
        if (!$this->cart) {
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            $item->quantity += 1;
            $item->recalculate();
        }

        $this->refreshCart();

        // Alpine.js uyumlu event dispatch
        $this->dispatch('cart-updated', [
            'cartId' => $this->cart->cart_id,
            'itemCount' => $this->itemCount,
            'total' => $this->total,
            'currencyCode' => $this->cart->currency_code ?? 'TRY',
        ]);
    }

    public function decreaseQuantity(int $cartItemId)
    {
        if (!$this->cart) {
            return;
        }

        $item = $this->cart->items()->find($cartItemId);

        if ($item) {
            if ($item->quantity > 1) {
                $item->quantity -= 1;
                $item->recalculate();
            } else {
                $item->delete();
            }
            $this->cart->recalculate();
        }

        $this->refreshCart();

        // Alpine.js uyumlu event dispatch
        $this->dispatch('cart-updated', [
            'cartId' => $this->cart->cart_id,
            'itemCount' => $this->itemCount,
            'total' => $this->total,
            'currencyCode' => $this->cart->currency_code ?? 'TRY',
        ]);
    }

    public function render()
    {
        return view('cart::livewire.front.cart-widget');
    }
}
