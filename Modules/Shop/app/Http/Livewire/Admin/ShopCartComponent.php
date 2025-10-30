<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use App\Traits\WithBulkActions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Shop\App\Services\ShopCartService;

#[Layout('admin.layout')]
class ShopCartComponent extends Component
{
    use WithPagination;
    use WithBulkActions;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sortField = 'cart_id';

    #[Url]
    public string $sortDirection = 'desc';

    #[Url]
    public string $status = '';

    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'cart_id'],
        'sortDirection' => ['except' => 'desc'],
        'status' => ['except' => ''],
    ];

    public function mount(): void
    {
        //
    }

    public function render()
    {
        $service = app(ShopCartService::class);

        $filters = [
            'search' => $this->search,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'status' => $this->status,
        ];

        $carts = $service->getPaginatedCartsForAdmin($filters, $this->perPage);

        return view('shop::admin.livewire.cart-component', [
            'carts' => $carts,
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteCart(int $cartId): void
    {
        $service = app(ShopCartService::class);
        $result = $service->deleteCartAdmin($cartId);

        if ($result['success']) {
            $this->dispatch('toast', [
                'title' => 'Success',
                'message' => $result['message'],
                'type' => 'success',
            ]);
        } else {
            $this->dispatch('toast', [
                'title' => 'Error',
                'message' => $result['message'],
                'type' => 'error',
            ]);
        }
    }

    public function markAsAbandoned(int $cartId): void
    {
        $service = app(ShopCartService::class);
        $result = $service->markAsAbandonedAdmin($cartId);

        if ($result['success']) {
            $this->dispatch('toast', [
                'title' => 'Success',
                'message' => $result['message'],
                'type' => 'success',
            ]);
        } else {
            $this->dispatch('toast', [
                'title' => 'Error',
                'message' => $result['message'],
                'type' => 'error',
            ]);
        }
    }

    public function bulkDeleteSelected(): void
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => 'Warning',
                'message' => 'Please select items to delete',
                'type' => 'warning',
            ]);
            return;
        }

        $service = app(ShopCartService::class);
        $deletedCount = $service->bulkDeleteCartsAdmin($this->selectedItems);

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('toast', [
            'title' => 'Success',
            'message' => "{$deletedCount} cart(s) deleted successfully",
            'type' => 'success',
        ]);
    }

    public function cleanOldCarts(): void
    {
        $service = app(ShopCartService::class);
        $deletedCount = $service->cleanOldCartsAdmin(30);

        $this->dispatch('toast', [
            'title' => 'Success',
            'message' => "{$deletedCount} old cart(s) cleaned successfully",
            'type' => 'success',
        ]);
    }
}
