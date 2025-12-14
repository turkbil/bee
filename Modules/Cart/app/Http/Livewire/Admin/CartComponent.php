<?php

namespace Modules\Cart\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\CartItem;

#[Layout('admin.layout')]
class CartComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $status = '';

    #[Url]
    public $perPage = 25;

    #[Url]
    public $includeGuests = false;

    public $selectedCart = null;
    public $showModal = false;
    public $cartIds = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'includeGuests' => ['except' => false],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function viewCart($cartId)
    {
        $this->selectedCart = Cart::with(['items.cartable', 'items.product', 'currency', 'customer'])
            ->find($cartId);

        if (!$this->selectedCart) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sepet bulunamadı',
                'type' => 'error',
            ]);
            return;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedCart = null;
    }

    public function clearCart($cartId)
    {
        $cart = Cart::find($cartId);

        if (!$cart) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sepet bulunamadı',
                'type' => 'error',
            ]);
            return;
        }

        $cart->clear();

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Sepet temizlendi',
            'type' => 'success',
        ]);

        $this->closeModal();
    }

    public function markAsAbandoned($cartId)
    {
        $cart = Cart::find($cartId);

        if (!$cart) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sepet bulunamadı',
                'type' => 'error',
            ]);
            return;
        }

        $cart->markAsAbandoned();

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Sepet "terk edildi" olarak işaretlendi',
            'type' => 'success',
        ]);

        $this->closeModal();
    }

    /**
     * Sepetten tekli ürün çıkar
     */
    public function removeItem($itemId)
    {
        $item = CartItem::find($itemId);

        if (!$item) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Ürün bulunamadı',
                'type' => 'error',
            ]);
            return;
        }

        $cartId = $item->cart_id;
        $item->delete();

        // Sepet toplamlarını güncelle
        $cart = Cart::find($cartId);
        if ($cart) {
            $cart->recalculateTotals();
        }

        // Modal'ı güncelle
        if ($this->selectedCart && $this->selectedCart->cart_id === $cartId) {
            $this->selectedCart = Cart::with(['items.cartable', 'items.product', 'currency', 'customer'])
                ->find($cartId);
        }

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Ürün sepetten çıkarıldı',
            'type' => 'success',
        ]);
    }

    /**
     * Ürün miktarını güncelle
     */
    public function updateItemQuantity($itemId, $quantity)
    {
        $item = CartItem::find($itemId);

        if (!$item) {
            return;
        }

        if ($quantity < 1) {
            $this->removeItem($itemId);
            return;
        }

        $item->quantity = $quantity;
        $item->subtotal = $item->unit_price * $quantity;
        $item->total = $item->subtotal + $item->tax_amount - $item->discount_amount;
        $item->save();

        // Sepet toplamlarını güncelle
        $cart = Cart::find($item->cart_id);
        if ($cart) {
            $cart->recalculateTotals();
        }

        // Modal'ı güncelle
        if ($this->selectedCart && $this->selectedCart->cart_id === $item->cart_id) {
            $this->selectedCart = Cart::with(['items.cartable', 'items.product', 'currency', 'customer'])
                ->find($item->cart_id);
        }
    }

    public function canGoNext()
    {
        if (!$this->selectedCart || empty($this->cartIds)) {
            return false;
        }

        $currentIndex = array_search($this->selectedCart->cart_id, $this->cartIds);
        return $currentIndex !== false && $currentIndex < count($this->cartIds) - 1;
    }

    public function canGoPrevious()
    {
        if (!$this->selectedCart || empty($this->cartIds)) {
            return false;
        }

        $currentIndex = array_search($this->selectedCart->cart_id, $this->cartIds);
        return $currentIndex !== false && $currentIndex > 0;
    }

    public function nextCart()
    {
        if (!$this->canGoNext()) {
            return;
        }

        $currentIndex = array_search($this->selectedCart->cart_id, $this->cartIds);
        $nextId = $this->cartIds[$currentIndex + 1];
        $this->viewCart($nextId);
    }

    public function previousCart()
    {
        if (!$this->canGoPrevious()) {
            return;
        }

        $currentIndex = array_search($this->selectedCart->cart_id, $this->cartIds);
        $previousId = $this->cartIds[$currentIndex - 1];
        $this->viewCart($previousId);
    }

    public function render()
    {
        $query = Cart::query()
            ->with(['items', 'currency', 'customer'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('cart_id', 'like', '%' . $this->search . '%')
                        ->orWhere('session_id', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            // Misafirler hariç (varsayılan) veya dahil
            ->when(!$this->includeGuests, function ($query) {
                $query->whereNotNull('customer_id');
            })
            // Önce üyeler, sonra aktivite tarihine göre sırala
            ->orderByRaw('CASE WHEN customer_id IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('last_activity_at', 'desc');

        $carts = $query->paginate($this->perPage);

        // Store cart IDs for navigation
        $this->cartIds = $query->pluck('cart_id')->toArray();

        // Statuses for filter
        $statuses = ['active', 'abandoned', 'converted', 'merged'];

        return view('cart::livewire.admin.cart-component', [
            'carts' => $carts,
            'statuses' => $statuses,
        ]);
    }
}
