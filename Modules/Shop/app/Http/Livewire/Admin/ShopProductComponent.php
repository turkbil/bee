<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use App\Traits\HasUniversalTranslation;
use Livewire\Attributes\{Layout, Url};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Shop\App\DataTransferObjects\ShopOperationResult;
use Modules\Shop\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActions};
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Services\ShopProductService;

#[Layout('admin.layout')]
class ShopProductComponent extends Component
{
    use WithPagination;
    use WithBulkActions;
    use InlineEditTitle;
    use HasUniversalTranslation;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $perPage = null;

    #[Url]
    public string $sortField = 'product_id';

    #[Url]
    public string $sortDirection = 'desc';

    public array $availableSiteLanguages = [];

    protected ShopProductService $productService;

    protected $listeners = [
        'refreshPageData' => 'refreshPageData',
    ];

    public function boot(ShopProductService $productService): void
    {
        $this->productService = $productService;
        $this->perPage ??= (int) config('modules.pagination.admin_per_page', 15);
        $this->availableSiteLanguages = TenantLanguage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }

    protected function getModelClass(): string
    {
        return ShopProduct::class;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = max(1, (int) $this->perPage);
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

    public function refreshPageData(): void
    {
        $this->productService->clearCache();
        $this->resetPage();
    }

    public function toggleActive(int $productId): void
    {
        $result = $this->productService->toggleProductStatus($productId);
        $this->emitResultToast($result);
    }

    public function bulkDeleteSelected(): void
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => __('shop::admin.select_records_first'),
                'type' => 'warning',
            ]);

            return;
        }

        $deleted = $this->productService->bulkDeleteProducts(array_map('intval', $this->selectedItems));

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => $result->message,
            'type' => 'success',
        ]);

        $this->refreshSelectedItems();
        $this->resetPage();
    }

    public function bulkToggleSelected(bool $status): void
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => __('shop::admin.select_records_first'),
                'type' => 'warning',
            ]);

            return;
        }

        $affected = $this->productService->bulkToggleProductStatus(array_map('intval', $this->selectedItems));

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => $result->message,
            'type' => $status ? 'success' : 'info',
        ]);

        $this->refreshSelectedItems();
        $this->resetPage();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $filters = [
            'search' => $this->search,
            'locales' => $this->availableSiteLanguages,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'currentLocale' => app()->getLocale(),
        ];

        $products = $this->productService->getPaginatedProducts($filters, (int) $this->perPage);

        return view('shop::admin.livewire.product-component', [
            'products' => $products,
            'siteLanguages' => $this->availableSiteLanguages,
            'currentSiteLocale' => app()->getLocale(),
        ]);
    }

    private function emitResultToast(ShopOperationResult $result): void
    {
        $this->dispatch('toast', [
            'title' => $result->success ? __('admin.success') : __('admin.' . $result->type),
            'message' => $result->message,
            'type' => $result->type,
        ]);

        if ($result->success) {
            $this->resetPage();
        }
    }
}
